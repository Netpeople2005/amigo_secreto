<?php

namespace KumbiaPHP\Di\Container;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Di\DependencyInjectionInterface as Di;
use KumbiaPHP\Di\Definition\Service;
use KumbiaPHP\Di\Exception\IndexNotDefinedException;

/**
 * Description of Container
 *
 * @author manuel
 */
class Container implements ContainerInterface
{

    /**
     * 
     * @var array
     */
    protected $services;

    /**
     *
     * @var Di 
     */
    protected $di;

    /**
     *
     * @var array 
     */
    protected $definitions;

    public function __construct(Di $di, array $definitions = array())
    {
        $this->services = array();
        $this->di = $di;
        $this->definitions = $definitions + array('parameters' => array(), 'services' => array());

        $di->setContainer($this);

        //agregamos al container como servicio.
        $this->setInstance('container', $this);
    }

    public function get($id)
    {

        //si no existe lanzamos la excepcion
        if (!$this->has($id)) {
            throw new IndexNotDefinedException(sprintf('No existe el servicio "%s"', $id));
        }
        //si existe el servicio y está creado lo devolvemos
        if ($this->hasInstance($id)) {
            return $this->services[$id];
        }
        //si existe pero no se ha creado, creamos la instancia
        $config = $this->definitions['services'][$id];

        //retorna la instancia recien creada
        return $this->di->newInstance($id, $config);
    }

    public function has($id)
    {
        return isset($this->definitions['services'][$id]);
    }

    public function hasInstance($id)
    {
        return isset($this->services[$id]);
    }

    /**
     * Establece una instancia de un objeto en el indice especificado
     * @param string $id indice
     * @param object $object objeto a almacenar
     */
    public function setInstance($id, $object)
    {
        $this->services[$id] = $object;
        //y lo agregamos a las definiciones. (solo será a gregado si no existe)
        if (!isset($this->definitions['services'][$id])) {

            $this->definitions['services'][$id] = array(
                'class' => get_class($object)
            );
        }
    }

    public function getParameter($id)
    {
        if ($this->hasParameter($id)) {
            return $this->definitions['parameters'][$id];
        } else {
            return NULL;
        }
    }

    public function hasParameter($id)
    {
        return array_key_exists($id, $this->definitions['parameters']);
    }

    
    public function getDefinitions()
    {
        return $this->definitions;
    }

    public function setParameter($id, $value)
    {
        $this->definitions['parameters'][$id] = $value;
        return $this;
    }

    /**
     * Crea ó Actualiza la configuración para la creación de un servicio.
     * 
     * @example $container->set("session", "Lib\\Session\Session", array(
     *              'construct' => '@request'
     * ));
     * 
     * @param string $id identificador del servicio
     * @param string $className Nombre de la Clase a Instanciar
     * @param array $config configuración para el servicio.
     */
    public function set($id, $className, array $config = array())
    {
        $config['class'] = $className;
        $this->definitions['services'][$id] = $config;
        return $this;
    }

    /**
     * Verifica la existencia de un serivicio ó un parametro en el contenedor.
     * @param string $offset
     * @return boolean 
     */
    public function offsetExists($offset)
    {
        return $this->has($offset) || $this->hasParameter($offset);
    }

    /**
     * Devuelve la instancia de una clase si está definida, sino devuelve un parametro,
     * si tampoco existe, devuelve null.
     * @param string $offset
     * @return mixed 
     */
    public function offsetGet($offset)
    {
        if ($this->has($offset)) {
            return $this->get($offset);
        } elseif ($this->hasParameter($offset)) {
            return $this->getParameter($offset);
        } else {
            return null;
        }
    }

    public function offsetSet($offset, $value)
    {
        //nada por ahora
    }

    public function offsetUnset($offset)
    {
        //nada por ahora
    }

}