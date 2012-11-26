<?php

namespace KumbiaPHP\Di;

use \ReflectionClass;
use KumbiaPHP\Di\Container\Container;
use KumbiaPHP\Di\Exception\DiException;
use KumbiaPHP\Di\DependencyInjectionInterface;
use KumbiaPHP\Di\Exception\IndexNotDefinedException;

/**
 * Clase inyectora de dependencias de los servicios en el FW
 * 
 * @author manuel
 */
class DependencyInjection implements DependencyInjectionInterface
{

    /**
     *  Contenedor de servicios
     *  @var Container
     */
    protected $container;

    /**
     * Contiene los servicios que se han ido solicitando a partir de un servicio
     * inicial que depende de otros servicios no creados aun.
     * 
     * Por cada solicitud de un servicio no creado, se debe verificar que ese
     * servicio no esté ya en la cola, porque estariamos en presencia de 
     * una dependencia circular entre servicios, donde un servicio A depende
     * de un servicio B que depende del servicio A.
     * 
     * @var array 
     */
    private $queue = array();

    /**
     * Indica si se le estan inyectando servicios a un elemento que ya estaba en
     * la cola y solicitó servicios que no habian sido creados aun.
     * @var type 
     */
    private $isQueue = FALSE;

    /**
     * {inherit}
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {inherit}
     */
    public function newInstance($id, $config)
    {
        if (!isset($config['class'])) {
            throw new IndexNotDefinedException("No se Encontró el indice \"class\" en la definicón del servicio \"$id\"");
        }

        $reflection = new ReflectionClass($config['class']);

        if (isset($config['factory'])) {
            $method = $config['factory']['method'];
            if (isset($config['factory']['argument'])) {
                $instance = $this->callFactory($reflection, $method, $config['factory']['argument']);
            } else {
                $instance = $this->callFactory($reflection, $method);
            }
        } else {

            if (isset($config['construct'])) {
                $arguments = $this->getArgumentsFromConstruct($id, $config);
            } else {
                $arguments = array();
            }

            //verificamos si ya se creó una instancia en una retrollamada del
            //metodo injectObjectIntoServicesQueue
            if (is_object($this->container->hasInstance($id))) {
                return $this->container->get($id);
            }

            $instance = $reflection->newInstanceArgs($arguments);
        }
        //agregamos la instancia del objeto al contenedor.
        $this->container->setInstance($id, $instance);

        $this->injectObjectIntoServicesQueue();

        if (isset($config['call'])) {
            $this->setOtherDependencies($id, $instance, $config['call']);
        }
        
        return $instance;
    }

    /**
     * Obtiene los valores para los argumentos del constructor
     * a partir de la configuración definida para el servicio.
     * 
     * @param string $id nombre del servico
     * @param array $config configuracion propia del servicio
     * @return array devuelve los valores para los argumentos del constructor 
     */
    protected function getArgumentsFromConstruct($id, array $config)
    {
        $args = array();
        //lo agregamos a la cola hasta que se creen los servicios del
        //que depende
        $this->addToQueue($id, $config);

        if (is_array($config['construct'])) {
            foreach ($config['construct'] as $serviceOrParameter) {
                if ('@' === $serviceOrParameter[0]) {//si comienza con @ es un servicio lo que solicita
                    $args[] = $this->container->get(substr($serviceOrParameter, 1));
                } else { //si no comienza por arroba es un parametro lo que solicita
                    $args[] = $this->container->getParameter($serviceOrParameter);
                }
            }
        } else {
            if ('@' === $config['construct'][0]) {//si comienza con @ es un servicio lo que solicita
                $args[] = $this->container->get(substr($config['construct'], 1));
            } else { //si no comienza por arroba es un parametro lo que solicita
                $args[] = $this->container->getParameter($config['construct']);
            }
        }
        //al tener los servicios que necesitamos
        //quitamos al servicio en construccion de la cola
        $this->removeToQueue($id);
        return $args;
    }

    /**
     * Inserta las demas dependencias en la instancia que se crea a traves
     * de metodos public de la clase.
     * 
     * @param string $id nombre del servicio
     * @param object $object instancia recien creado
     * @param array $calls arreglo con los nombres de los metodos a llamar y
     * los sercicios que estos metodos esperan
     */
    protected function setOtherDependencies($id, $object, array $calls)
    {
        foreach ($calls as $method => $serviceOrParameter) {
            if ('@' === $serviceOrParameter[0]) {//si comienza con @ es un servicio lo que solicita
                $object->$method($this->container->get(substr($serviceOrParameter, 1)));
            } else { //si no comienza por arroba es un parametro lo que solicita
                $object->$method($this->container->getParameter($serviceOrParameter));
            }
        }
    }

    /**
     * Inyecta el servicio recien creado en los servicios que lo están
     * solicitando.
     * 
     * Debe activarse el semaforo isQueue para avisar al inyector
     * de que ya existe el servicio en la cola y que no debe volver a ser
     * agregado. 
     */
    protected function injectObjectIntoServicesQueue()
    {
        $this->isQueue = TRUE;
        foreach ($this->queue as $id => $config) {
            $this->newInstance($id, $config);
        }
        $this->isQueue = FALSE;
    }

    protected function inQueue($id)
    {
        return isset($this->queue[$id]);
    }

    /**
     * Cuando un servicio está solicitando una instancia de otro servicio que
     * aun no existe, el servicio actual pasa a una cola de servicios que esperan
     * por la creación de las instancias que necesitan.
     *  
     * @param string $id nombre del servicio
     * @param array $config configuracion del servicio
     * @throws LogicException si se está agregando a un servicio que ya está en la
     * cola de servicios y no esta activada la variable "isQueue" que indica que
     * estamos inyectando servicios a uno de la cola, significa que hay una dependencia
     * que no se puede resolver. ejemplo de ello es un servicio A que en el constructor
     * espera la instancia de un servicio B que en el constructor espera una 
     * instancia del servicio A.
     */
    protected function addToQueue($id, $config)
    {
        //si el servicio actual aparece en la cola de servicios
        //indica que dicho servicio tiene una dependencia a un servicio 
        //que depende de este, por lo que hay una dependencia circular.
        if (!$this->isQueue && $this->inQueue($id)) {
            throw new \LogicException("Se ha Detectado una Dependencia Circular entre Servicios");
        }
        $this->queue[$id] = $config;
    }

    /**
     * Quita un servicio de la cola de servicios en espera.
     * @param string $id nombre del servicio
     */
    protected function removeToQueue($id)
    {
        if ($this->inQueue($id)) {
            unset($this->queue[$id]);
        }
    }

    /**
     * Obtiene la instancia del servicio a travez del llamado al metodo estático
     * de la clase.
     * 
     * @param \ReflectionClass $class clase a la que se le va hacer el factory
     * @param string $method nombre del método que hace el factory
     * @param string $argument nombre del servicio ó parametro a pasar al método
     * @return object
     * @throws DiException 
     */
    protected function callFactory(\ReflectionClass $class, $method, $argument = NULL)
    {
        if (!$class->hasMethod($method)) {
            throw new DiException("No existe el Método \"$method\" en la clase \"{$class->name}\"");
        }

        $method = $class->getMethod($method);

        if (!$method->isStatic()) {
            throw new DiException("El Método \"$method\" de la clase \"{$class->name}\" debe ser Estático");
        }

        if ('@' === $argument[0]) {//si comienza con @ es un servicio lo que solicita
            $argument = $this->container->get(substr($argument, 1));
        } elseif ($argument) { //si no comienza por arroba es un parametro lo que solicita
            $argument = $this->container->getParameter($argument);
        }

        $class = $method->invoke(NULL, $argument);

        if (!is_object($class)) {
            throw new DiException("El Método \"$method\" de la clase \"{$class->name}\" debe retornar un Objeto");
        }

        return $class;
    }

}