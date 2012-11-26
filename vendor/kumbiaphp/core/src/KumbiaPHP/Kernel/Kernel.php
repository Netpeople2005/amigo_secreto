<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Loader\Autoload;
use KumbiaPHP\Kernel\AppContext;
use KumbiaPHP\Di\Definition\Service;
use KumbiaPHP\Di\DependencyInjection;
use KumbiaPHP\Di\Container\Container;
use KumbiaPHP\Kernel\KernelInterface;
use KumbiaPHP\Di\Definition\Parameter;
use KumbiaPHP\Kernel\Event\KumbiaEvents;
use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Kernel\Event\ResponseEvent;
use KumbiaPHP\Kernel\Config\ConfigReader;
use KumbiaPHP\Kernel\Event\ExceptionEvent;
use KumbiaPHP\Kernel\Event\ControllerEvent;
use KumbiaPHP\Di\Definition\DefinitionManager;
use KumbiaPHP\EventDispatcher\EventDispatcher;
use KumbiaPHP\Kernel\Exception\ExceptionHandler;
use KumbiaPHP\Kernel\Controller\ControllerResolver;

/**
 * Kernel del FW
 *
 * @author manuel
 */
abstract class Kernel implements KernelInterface
{

    /**
     * Arreglo con los namespaces definidos en la aplicación
     * @var array 
     */
    protected $modules;

    /**
     * Arreglo con los prefijos de las rutas para los modulos usados en la aplicación
     * @var array 
     */
    protected $routes;

    /**
     * Objeto inyector de dependencias
     *
     * @var DependencyInjection
     */
    protected $di;

    /**
     * Objeto contenedor de servicios
     *  
     * @var Container
     */
    protected static $container;

    /**
     * Objeto Request
     *
     * @var Request 
     */
    protected $request;

    /**
     * Objeto despachador de eventos
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * Indica si la aplicación está ó no en producción
     * @var boolean 
     */
    protected $production;

    /**
     * ruta hacia la carpeta app del fw 
     * @var string
     */
    protected $appPath;

    /**
     * Constructor de la clase. 
     * 
     * La instancia del kernel se crea en proyecto/public/index.php
     * 
     * @param boolean $production indica si estamos en producción ó no.
     */
    public function __construct($production = FALSE)
    {
        ob_start(); //arrancamos el buffer de salida.
        $this->production = $production;

        Autoload::registerDirectories(
                $this->modules = $this->registerModules()
        );

        Autoload::register();

        ExceptionHandler::handle($this);

        if ($production) {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        } else {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        }

        $this->routes = $this->registerRoutes();
    }

    /**
     * Inicializa las clases basicas del fw.
     * 
     * AppContext, Container, Inyector de dependencias, Dispatcher, etc.
     *  
     */
    public function init(Request $request)
    {
        //creamos la instancia del AppContext
        $context = new AppContext($this->production, $this->getAppPath(), $this->modules, $this->routes);
        //leemos la config de la app
        $config = new ConfigReader($context);
        //iniciamos el container con esa config
        $this->initContainer($config->getConfig());
        //asignamos el kernel al container como un servicio
        self::$container->setInstance('app.kernel', $this);
        //iniciamos el dispatcher con esa config
        $this->initDispatcher($config->getConfig());
        //seteamos el contexto de la aplicación como servicio
        self::$container->setInstance('app.context', $context);
        //establecemos el Request en el AppContext
        $context->setLocales(self::$container->getParameter('config.locales'));
        $context->setRequest($request);
    }

    public function execute(Request $request, $type = Kernel::MASTER_REQUEST)
    {
        try {
            //verificamos el tipo de petición
            if (self::MASTER_REQUEST === $type) {
                return $this->_execute($request, $type);
            } else {
                //almacenamos en una variable temporal el request
                //original. y actualizamos el AppContext.
                //tambien el tipo de request
                $originalRequest = $this->request;
                $originalRequestType = self::$container->get('app.context')
                        ->getRequestType();
                self::$container->get('app.context')
                        ->setRequest($request)
                        ->setRequestType($type);

                $response = $this->_execute($request, $type);

                //Luego devolvemos el request original al kernel,
                //al AppContext, y el tipo de request
                $this->request = $originalRequest;
                self::$container->setInstance('request', $originalRequest);
                self::$container->get('app.context')
                        ->setRequest($originalRequest)
                        ->setRequestType($originalRequestType);

                return $response;
            }
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

    private function _execute(Request $request, $type = Kernel::MASTER_REQUEST)
    {
        $this->request = $request;

        if (!self::$container) { //si no se ha creado el container lo creamos.
            $this->init($request);
            self::$container->get('app.context')->setRequestType($type);
            $this->production || $this->validateModules();
        }
        //agregamos el request al container
        self::$container->setInstance('request', $this->request);

        //ejecutamos el evento request
        $this->dispatcher->dispatch(KumbiaEvents::REQUEST, $event = new RequestEvent($request));

        if (!$event->hasResponse()) {

            //creamos el resolver.
            $resolver = new ControllerResolver(self::$container);
            //obtenemos la instancia del controlador, el nombre de la accion
            //a ejecutar, y los parametros que recibirá dicha acción a traves del método
            //getController del $resolver y lo pasamos al ControllerEvent
            $event = new ControllerEvent($request, $resolver->getController($request));
            //ejecutamos el evento controller.
            $this->dispatcher->dispatch(KumbiaEvents::CONTROLLER, $event);

            //ejecutamos la acción de controlador pasandole los parametros.
            $response = $resolver->executeAction($event);
            if (!$response instanceof Response) {
                $response = $this->createResponse($resolver);
            }
        } else {
            $response = $event->getResponse();
        }

        return $this->response($response);
    }

    /**
     * Crea una respuesta a partir de  la vista y template seleccionados
     * en el controlador.
     * @param ControllerResolver $resolver
     * @return Response
     */
    private function createResponse(ControllerResolver $resolver)
    {
        //como la acción no devolvió respuesta, debemos
        //obtener la vista y el template establecidos en el controlador
        //para pasarlos al servicio view, y este construya la respuesta
        $view = $resolver->callMethod('getView');
        $template = $resolver->callMethod('getTemplate');
        $cache = $resolver->callMethod('getCache');
        $properties = $resolver->getPublicProperties(); //nos devuelve las propiedades publicas del controlador
        //llamamos al render del servicio "view" y esté nos devolverá
        //una instancia de response con la respuesta creada
        return self::$container->get('view')->render($template, $view, $properties, $cache);
    }

    private function exception(\Exception $e)
    {
        $event = new ExceptionEvent($e, $this->request);
        $this->dispatcher->dispatch(KumbiaEvents::EXCEPTION, $event);

        if ($event->hasResponse()) {
            return $this->response($event->getResponse());
        }

        if ($this->production) {
            return ExceptionHandler::createException($e);
        }

        throw $e;
    }

    private function response(Response $response)
    {
        $event = new ResponseEvent($this->request, $response);
        //ejecutamos el evento response.
        $this->dispatcher->dispatch(KumbiaEvents::RESPONSE, $event);
        //retornamos la respuesta
        return $event->getResponse();
    }

    /**
     *
     * @return boolean 
     */
    public function isProduction()
    {
        return $this->production;
    }

    public static function get($service)
    {
        return self::$container->get($service);
    }

    public static function getParam($param)
    {
        return self::$container->getParameter($param);
    }

    /**
     * clase abstracta que está implementada en el AppKernel de la carpeta app
     * del proyecto, donde se especifican las rutas y los modulos que trabajará 
     * la aplicación, permite instalar librerias, etc.
     */
    abstract protected function registerModules();

    /**
     * clase abstracta que está implementada en el AppKernel de la carpeta app
     * del proyecto, donde se especificarán en un arreglo los prefijos de rutas
     * que tendran los modulos de la aplicación.
     */
    abstract protected function registerRoutes();

    /**
     * devuelve la ruta a la carpeta app del proyecto.
     * @return string 
     */
    public function getAppPath()
    {
        if (!$this->appPath) {
            $r = new \ReflectionObject($this);
            $this->appPath = dirname($r->getFileName()) . '/';
        }
        return $this->appPath;
    }

    /**
     * Esta función inicializa el contenedor de servicios.
     * @param Collection $reader toda la configuracion de los archivos de config
     * de cada lib y modulo compilados en uno solo.
     */
    protected function initContainer(array $config = array())
    {
        $definitions = array(
            'services' => $config['services'],
            'parameters' => $config['parameters'],
        );

        $definitions['parameters']['app_dir'] = $this->getAppPath();

        $this->di = new DependencyInjection();

        self::$container = new Container($this->di, $definitions);

        //si se estan usando locales y ningun módulo a establecido una definición para
        //el servicio translator, lo hacemos por acá.
        if (isset($definitions['parameters']['config.locales'])
                && !self::$container->has('translator')) {
            self::$container->set('translator', 'KumbiaPHP\\Translation\\Translator');
        }
    }

    /**
     * Inicializa el despachador de eventos
     * @param Collection $reader config de todo el proyecto.
     */
    protected function initDispatcher(array $config = array())
    {
        $this->dispatcher = new EventDispatcher(self::$container);
        foreach ($config['services'] as $service => $params) {
            if (isset($params['listen'])) {
                foreach ($params['listen'] as $method => $event) {
                    $this->dispatcher->addListener($event, array($service, $method));
                }
            }
        }

        self::$container->setInstance('dispatcher', $this->dispatcher);
    }

    private function validateModules()
    {
        foreach ($this->modules as $module => $path) {
            if (false === is_dir($route = rtrim($path, '/') . "/{$module}")) {
                throw new \InvalidArgumentException("No existe la ruta \"$route\" para el módulo \"$module\"");
            }
        }
    }

}