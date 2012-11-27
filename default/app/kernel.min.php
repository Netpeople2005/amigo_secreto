<?php


namespace KumbiaPHP\Kernel;


class Collection implements \Serializable
{

    
    protected $params;

    
    function __construct(array $params = array())
    {
        $this->params = $params;
    }

    
    public function has($key)
    {
        return array_key_exists($key, $this->params);
    }

    
    public function get($key, $default = NULL)
    {
        return $this->has($key) ? $this->params[$key] : $default;
    }

    
    public function set($key, $value)
    {
        $this->params[$key] = $value;
    }

    
    public function all()
    {
        return $this->params;
    }

    
    public function count()
    {
        return count($this->params);
    }

    
    public function delete($key)
    {
        if ($this->has($key)) {
            unset($this->params[$key]);
        }
    }

    
    public function clear()
    {
        $this->params = array();
    }

    
    public function serialize()
    {
        return serialize($this->params);
    }

    
    public function unserialize($serialized)
    {
        $this->params = unserialize($serialized);
    }

    
    public function keys()
    {
        return array_keys($this->all());
    }

    
    public function getInt($key, $default = 0)
    {
        return (int) $this->get($key, $default);
    }

    
    public function getDigits($key, $default = '')
    {
        return preg_replace('/[^[:digit:]]/', '', $this->get($key, $default));
    }

    
    public function getAlnum($key, $default = '')
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }

    
    public function getAlpha($key, $default = '')
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }

}

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Collection;

class CookiesCollection extends Collection
{

    
    public function has($key)
    {
        return array_key_exists($key, $_COOKIE);
    }

    
    public function get($key, $default = NULL)
    {
        return $this->has($key) ? $_COOKIE[$key] : $default;
    }

    
    public function set($key, $value, $expire = 0)
    {
        setcookie($key, $value, $expire);
    }

    
    public function all()
    {
        return (array) $_COOKIE;
    }

    
    public function count()
    {
        return count($_COOKIE);
    }

    
    public function delete($key)
    {
        if ($this->has($key)) {
            $this->set($key, false);
        }
    }

    
    public function clear()
    {
        foreach ($this->keys() as $cookie) {
            $this->delete($cookie);
        }
    }

}

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\File;
use KumbiaPHP\Kernel\Collection;

class FilesCollection extends Collection
{

    public function __construct()
    {
        foreach ((array) $_FILES as $name => $data) {
            $this->set($name, new File($data));
        }
    }

}

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Collection;
use KumbiaPHP\Kernel\AppContext;
use KumbiaPHP\Kernel\FilesCollection;
use KumbiaPHP\Kernel\CookiesCollection;
use KumbiaPHP\Kernel\Session\SessionInterface;


class Request
{

    
    public $server;

    
    public $request;

    
    public $query;

    
    public $cookies;

    
    public $files;

    
    protected $app;

    
    private $baseUrl;

    
    protected $content = FALSE;
    protected $locale;

    
    public function __construct($baseUrl = NULL)
    {
        $this->server = new Collection($_SERVER);
        $this->request = new Collection($_POST);
        $this->query = new Collection($_GET);
        $this->cookies = new CookiesCollection();
        $this->files = new FilesCollection();

        //este fix es para permitir tener en el request los valores para peticiones
        //PUT y DELETE, ya que php no ofrece una forma facil de obtenerlos
        //actualmente.
        if (0 === strpos($this->server->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
                && in_array($this->getMethod(), array('PUT', 'DELETE'))
        ) {
            parse_str($this->getContent(), $data);
            $this->request = new Collection($data);
        } elseif (0 === strpos($this->server->get('CONTENT_TYPE'), 'application/json')) {
            //si los datos de la petición se envian en formato JSON
            //los convertimos en una arreglo.
            $this->request = new Collection((array) json_decode($this->getContent(), TRUE));
        }

        if ($baseUrl) {
            $this->baseUrl = $baseUrl;
        } else {
            $this->baseUrl = $this->createBaseUrl();
        }
    }

    
    public function get($key, $default = NULL)
    {
        //busca en request, si no existe busca en query sino existe busca en 
        //cookies, si no devuelve $default.
        return $this->request->get($key, $this->query->get($key, $this->cookies->get($key, $default)));
    }

    
    public function getAppContext()
    {
        return $this->app;
    }

    
    public function setAppContext(AppContext $app)
    {
        $this->app = $app;
    }

    
    public function getMethod()
    {
        return $this->server->get('REQUEST_METHOD', 'GET');
    }

    
    public function getClientIp()
    {
        return $this->server->get('REMOTE_ADDR');
    }

    
    public function isAjax()
    {
        return $this->server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    
    public function isMethod($method)
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }

    
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    
    public function getRequestUrl()
    {
        return $this->query->get('_url', '/');
    }

    
    public function getContent()
    {
        if (FALSE === $this->content) {
            $this->content = file_get_contents('php://input');
        }
        return $this->content;
    }

    public function __clone()
    {
        $this->__construct($this->getBaseUrl());
    }

    
    public function getLocale()
    {
        return $this->locale;
    }

    
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    
    private function createBaseUrl()
    {
        $uri = $this->server->get('REQUEST_URI');
        if ($qString = $this->server->get('QUERY_STRING')) {
            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }
            return str_replace($this->getRequestUrl(), '/', urldecode($uri));
        } else {
            return $uri;
        }
    }

}

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Exception\NotFoundException;


class AppContext
{

    
    protected $appPath;

    
    protected $modulesPath;

    
    protected $modules;

    
    protected $routes;

    
    protected $currentModule;

    
    protected $currentModuleUrl;

    
    protected $currentController;

    
    protected $currentAction;

    
    protected $currentParameters;

    
    protected $inProduction;

    
    protected $requestType;

    
    protected $locales;

    
    protected $request;

    
    public function __construct($inProduction, $appPath, $modules, $routes)
    {
        $this->inProduction = $inProduction;
        $this->appPath = $appPath;
        $this->modulesPath = rtrim($appPath, '/') . '/modules/';
        $this->modules = $modules;
        $this->routes = $routes;
    }

    
    public function setRequest(Request $request)
    {
        $request->setAppContext($this);
        $this->request = $request;
        $this->parseUrl();
        return $this;
    }

    public function setLocales($locales = null)
    {
        $this->locales = explode(',', $locales);
        return $this;
    }

    
    public function setRequestType($type)
    {
        $this->requestType = $type;
        return $this;
    }

    
    public function getRequestType()
    {
        return $this->requestType;
    }

    
    public function getBaseUrl()
    {
        return $this->request->getBaseUrl();
    }

    
    public function getAppPath()
    {
        return $this->appPath;
    }

    
    public function getRequestUrl()
    {
        return $this->request->getRequestUrl();
    }

    
    public function getPath($module)
    {
        if (isset($this->modules[$module])) {
            return rtrim($this->modules[$module], '/') . "/{$module}/";
        } else {
            return NULL;
        }
    }

    
    public function getModules($module = NULL)
    {
        if ($module) {
            return isset($this->modules[$module]) ? $this->modules[$module] : NULL;
        } else {
            return $this->modules;
        }
    }

    
    public function getRoutes($route = NULL)
    {
        if ($route) {
            if (isset($this->routes[$route])) {
                return isset($this->modules[$this->routes[$route]]) ? $this->routes[$route] : NULL;
            } else {
                return NULL;
            }
        } else {
            return $this->routes;
        }
    }

    
    public function getCurrentModule()
    {
        return $this->currentModule;
    }

    
    public function setCurrentModule($currentModule)
    {
        $this->currentModule = $currentModule;
        return $this;
    }

    
    public function getCurrentController()
    {
        return $this->currentController;
    }

    
    public function setCurrentController($currentController)
    {
        $this->currentController = $currentController;
        return $this;
    }

    
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    
    public function setCurrentAction($currentAction)
    {
        $this->currentAction = $currentAction;
        return $this;
    }

    
    public function getCurrentParameters()
    {
        return $this->currentParameters;
    }

    
    public function setCurrentParameters(array $currentParameters = array())
    {
        $this->currentParameters = $currentParameters;
        return $this;
    }

    
    public function getCurrentUrl($parameters = FALSE)
    {
        $url = $this->createUrl("{$this->currentModule}:{$this->currentController}/{$this->currentAction}");
        if ($parameters && count($this->currentParameters)) {
            $url .= '/' . join('/', $this->currentParameters);
        }
        return $url;
    }

    
    public function InProduction()
    {
        return $this->inProduction;
    }

    
    public function getControllerUrl($action = null)
    {
        return rtrim($this->createUrl("{$this->currentModule}:{$this->currentController}/{$action}"), '/');
    }

    
    public function getCurrentModuleUrl()
    {
        return $this->currentModuleUrl;
    }

    
    public function setCurrentModuleUrl($currentModuleUrl)
    {
        $this->currentModuleUrl = $currentModuleUrl;
        return $this;
    }

    
    public function createUrl($url, $baseUrl = true)
    {
        $url = explode(':', $url);
        if (count($url) > 1) {
            if (!$route = array_search($url[0], $this->routes)) {
                throw new NotFoundException("No Existe el módulo {$url[0]}, no se pudo crear la url");
            }
            $url = ltrim(trim($route, '/') . '/' . $url[1], '/');
        } else {
            $url = ltrim($url[0], '/');
        }
        //si se usa locale, lo añadimos a la url.
        $this->request->getLocale() && $url = $this->request->getLocale() . '/' . $url;
        return $baseUrl ? $this->request->getBaseUrl() . $url : $url;
    }

    
    public function parseUrl()
    {
        $controller = 'index'; //controlador por defecto si no se especifica.
        $action = 'index'; //accion por defecto si no se especifica.
        $moduleUrl = '/';
        $params = array(); //parametros de la url, de existir.
        //obtenemos la url actual de la petición.
        $currentUrl = '/' . trim($this->request->getRequestUrl(), '/');

        list($moduleUrl, $module, $currentUrl) = $this->getModule($currentUrl);

        if (!$moduleUrl || !$module) {
            throw new NotFoundException(sprintf("La ruta \"%s\" no concuerda con ningún módulo ni controlador en la App", $currentUrl), 404);
        }

        if ($url = explode('/', trim(substr($currentUrl, strlen($moduleUrl)), '/'))) {

            //ahora obtengo el controlador
            if (current($url)) {
                //si no es un controlador lanzo la excepcion
                $controller = current($url);
                next($url);
            }
            //luego obtenemos la acción
            if (current($url)) {
                $action = current($url);
                next($url);
            }
            //por ultimo los parametros
            if (current($url)) {
                $params = array_slice($url, key($url));
            }
        }

        $this->setCurrentModule($module)
                ->setCurrentModuleUrl($moduleUrl)
                ->setCurrentController($controller)
                ->setCurrentAction($action)
                ->setCurrentParameters($params);
    }

    
    private function camelcase($string)
    {
        return str_replace(' ', '', ucwords(preg_replace('@(.+)_(\w)@', '$1 $2', strtolower($string))));
    }

    
    protected function getModule($url, $recursive = true)
    {
        if (count($this->locales) && $recursive) {
            $_url = explode('/', trim($url, '/'));
            $locale = array_shift($_url);
            if (in_array($locale, $this->locales)) {
                $this->request->setLocale($locale);
                return $this->getModule('/' . join('/', $_url), false);
            } else {
                $this->request->setLocale($this->locales[0]);
            }
        }

        if ('/logout' === $url) {
            return array($url, $url, $url);
        }

        $routes = array_keys($this->getRoutes());

        usort($routes, function($a, $b) {
                    return strlen($a) > strlen($b) ? -1 : 1;
                }
        );

        foreach ($routes as $route) {
            if (0 === strpos($url, $route)) {
                if ('/' === $route) {
                    return array($route, $this->getRoutes('/'), $url);
                } elseif ('/' === substr($url, strlen($route), 1) || strlen($url) === strlen($route)) {
                    return array($route, $this->getRoutes($route), $url);
                }
            }
        }
        return false;
    }

}



namespace KumbiaPHP\Kernel\Config;

use KumbiaPHP\Kernel\AppContext;


class ConfigReader
{

    
    protected $config;
    private $sectionsValid = array('config', 'parameters');

    public function __construct(AppContext $app)
    {
        $configFile = $app->getAppPath() . '/config/config.php';
        if ($app->inProduction()) {
            if (is_file($configFile)) {
                $this->config = require_once $configFile;
                return;
            } else {
                $this->config = $this->compile($app);
                $config = PHP_EOL . PHP_EOL . 'return '
                        . var_export($this->config, true);
                file_put_contents($configFile, "<?php$config;");
            }
        } else {
            $this->config = $this->compile($app);
            if (is_writable($configFile)) {
                unlink($configFile);
            }
        }
    }

    
    protected function compile(AppContext $app)
    {
        $section['config'] = array();
        $section['services'] = array();
        $section['parameters'] = array();

        $dirs = array_merge($app->getModules(), array('app' => dirname($app->getAppPath())));

        foreach ($dirs as $namespace => $dir) {
            $configFile = rtrim($dir, '/') . '/' . $namespace . '/config/config.ini';
            $servicesFile = rtrim($dir, '/') . '/' . $namespace . '/config/services.ini';

            if (is_file($configFile)) {
                foreach (parse_ini_file($configFile, TRUE) as $sectionType => $values) {

                    if (in_array($sectionType, $this->sectionsValid)) {
                        foreach ($values as $index => $v) {
                            $section[$sectionType][$index] = $v;
                        }
                    }
                }
            }
            if (is_file($servicesFile)) {
                foreach (parse_ini_file($servicesFile, TRUE) as $serviceName => $config) {
                    $section['services'][$serviceName] = $config;
                }
            }
        }

        $section = $this->explodeIndexes($section);

        unset($section['config']); //esta seccion esta disponible en parameters con el prefio config.*

        return $this->prepareAditionalConfig($section);
    }

    public function getConfig()
    {
        return $this->config;
    }

    
    protected function explodeIndexes(array $section)
    {
        foreach ($section['config'] as $key => $value) {
            $explode = explode('.', $key);
            //si hay un punto y el valor delante del punto
            //es el nombre de un servicio existente
            if (count($explode) > 1 && isset($section['services'][$explode[0]])) {
                //le asignamos el nuevo valor al parametro
                //que usará ese servicio
                if (isset($section['parameters'][$explode[1]])) {
                    $section['parameters'][$explode[1]] = $value;
                }
            } else {
                $section['parameters']['config.' . $key] = $value;
            }
        }
        return $section;
    }

    
    protected function prepareAditionalConfig($configs)
    {
        //si se usa el routes lo añadimos al container
        if (isset($configs['parameters']['config.routes'])) {
            $router = substr($configs['parameters']['config.routes'], 1);

            //si es el router por defecto quien reescribirá las url
            if ('router' === $router) {
                //solo le añadimos un listener.
                $configs['services']['router']
                        ['listen']['rewrite'] = 'kumbia.request';
            } else {
                //si es un servicio distinto al router. y existe,
                //lo añadimos al principio de todos los servicios.
                $def = $configs['services'][$router]; //guardamos la definición del servicio en una variable temporal.
                unset($configs['services'][$router]); //eliminamos la definición del arreglo $config
                //volteamos el arreglo de servicios, para insertar de nuevo la definición.
                $configs['services'] = array_reverse($configs['services'], true);
                //insertamos la definición, quedando esta al final del array.
                $configs['services'][$router] = $def;
                //volvemos a voltear los servicios, con lo que la definición insertada
                //queda de primera.
                $configs['services'] = array_reverse($configs['services'], true);
                //esto es importante debido a que queremos que el primer escucha que siempre
                //se ejecute sea el que hace las reescrituras de url, para que cuando se
                //llamen a los siguientes escuchas ya la url esté reescrita.
            }
        }

        //si se estan usando locales y ningun módulo a establecido una definición para
        //el servicio translator, lo hacemos por acá.
        if (isset($configs['parameters']['config.locales'])
                && !isset($configs['services']['translator'])) {
            $configs['services']['translator'] = array(
                'class' => 'KumbiaPHP\\Translation\\Translator',
            );
        }

        return $configs;
    }

}

namespace KumbiaPHP\Di;

use KumbiaPHP\Di\Container\Container;


interface DependencyInjectionInterface
{

    
    public function newInstance($id, $config);

    
    public function setContainer(Container $container);
}


namespace KumbiaPHP\Di;

use \ReflectionClass;
use KumbiaPHP\Di\Container\Container;
use KumbiaPHP\Di\Exception\DiException;
use KumbiaPHP\Di\DependencyInjectionInterface;
use KumbiaPHP\Di\Exception\IndexNotDefinedException;


class DependencyInjection implements DependencyInjectionInterface
{

    
    protected $container;

    
    private $queue = array();

    
    private $isQueue = FALSE;

    
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    
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

    
    protected function removeToQueue($id)
    {
        if ($this->inQueue($id)) {
            unset($this->queue[$id]);
        }
    }

    
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

namespace KumbiaPHP\Di\Container;


interface ContainerInterface extends \ArrayAccess
{

    
    public function get($id);

    
    public function has($id);

    
    public function hasInstance($id);

    
    public function getParameter($id);

    
    public function hasParameter($id);

    
    public function setParameter($id, $value);
}


namespace KumbiaPHP\Di\Container;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Di\DependencyInjectionInterface as Di;
use KumbiaPHP\Di\Definition\Service;
use KumbiaPHP\Di\Exception\IndexNotDefinedException;


class Container implements ContainerInterface
{

    
    protected $services;

    
    protected $di;

    
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

    
    public function set($id, $className, array $config = array())
    {
        $config['class'] = $className;
        $this->definitions['services'][$id] = $config;
        return $this;
    }

    
    public function offsetExists($offset)
    {
        return $this->has($offset) || $this->hasParameter($offset);
    }

    
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

namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\Event;


interface EventDispatcherInterface
{

    
    public function dispatch($eventName, Event $event);

    
    public function addListener($eventName, $listener);

    
    public function hasListener($eventName, $listener);

    
    public function removeListener($eventName, $listener);
}

namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\EventDispatcherInterface;
use KumbiaPHP\Di\Container\ContainerInterface;


class EventDispatcher implements EventDispatcherInterface
{

    
    protected $listeners = array();

    
    protected $container;

    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function dispatch($eventName, Event $event)
    {
        if (!array_key_exists($eventName, $this->listeners)) {
            return;
        }
        if (is_array($this->listeners[$eventName]) && count($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $service = $this->container->get($listener[0]);
                $service->{$listener[1]}($event);
                if ($event->isPropagationStopped()) {
                    return;
                }
            }
        }
    }

    public function addListener($eventName, $listener)
    {
        if (!$this->hasListener($eventName, $listener)) {
            $this->listeners[$eventName][] = $listener;
        }
    }

    public function hasListener($eventName, $listener)
    {
        if (isset($this->listeners[$eventName])) {
            return in_array($listener, $this->listeners[$eventName]);
        } else {
            return FALSE;
        }
    }

    public function removeListener($eventName, $listener)
    {
        if ($this->hasListener($eventName, $listener)) {
            do {
                if ($listener === current($this->listeners[$eventName])) {
                    $key = key(current($this->listeners[$eventName]));
                    break;
                }
            } while (next($this->listeners[$eventName]));
        }
        unset($this->listeners[$eventName][$key]);
    }

}

namespace KumbiaPHP\Kernel\Event;


final class KumbiaEvents
{

    const REQUEST = 'kumbia.request';
    const CONTROLLER = 'kumbia.controller';
    const RESPONSE = 'kumbia.response';
    const EXCEPTION = 'kumbia.exception';

}

namespace KumbiaPHP\EventDispatcher;


class Event
{

    
    protected $propagationStopped = FALSE;

    
    public function stopPropagation()
    {
        $this->propagationStopped = TRUE;
    }

    
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

}

namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\EventDispatcher\Event;


class RequestEvent extends Event
{

    
    protected $request;

    
    protected $response;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    
    public function getRequest()
    {
        return $this->request;
    }

    
    public function hasResponse()
    {
        return $this->response instanceof Response;
    }

    
    public function getResponse()
    {
        return $this->response;
    }

    
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

}

namespace KumbiaPHP\Kernel\Controller;

use \ReflectionClass;
use \ReflectionObject;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Kernel\Event\ControllerEvent;
use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Kernel\Exception\NotFoundException;


class ControllerResolver
{

    
    protected $container;

    
    protected $module;

    
    protected $controller;

    
    protected $controllerName;

    
    protected $action;

    
    protected $parameters;

    public function __construct(ContainerInterface $con)
    {
        $this->container = $con;

        $app = $con->get('app.context');

        $this->module = $app->getCurrentModule();
        $this->controllerUrl = $app->getCurrentController();
        $this->action = $app->getCurrentAction() . '_action';
        $this->parameters = $app->getCurrentParameters();
    }

    
    public function getController()
    {
        if ('/logout' === $this->module) {
            throw new NotFoundException(sprintf("La ruta \"%s\" no concuerda con ningún módulo ni controlador en la App", $this->module), 404);
        }

        $app = $this->container->get('app.context');
        $this->controllerName = $app->getCurrentController() . 'Controller';
        //uno el namespace y el nombre del controlador.
        $controllerClass = str_replace('/', '\\', $this->module) . "\\Controller\\{$this->controllerName}";

        try {

            $controllerFile = "{$app->getModules($this->module)}{$controllerClass}.php";

            if (!is_file($controllerFile)) {
                throw new NotFoundException();
            }

            require_once $controllerFile;

            $reflectionClass = new ReflectionClass($controllerClass);
            if ($reflectionClass->getShortName() !== $this->controllerName) {
                throw new NotFoundException();
            }
        } catch (\Exception $e) {
            $modulePath = $app->getPath($this->module);
            throw new NotFoundException(sprintf("No existe el controlador \"%s\" en la ruta \"%sController/%s.php\"", $this->controllerName, $modulePath, $this->controllerName), 404);
        }

        $this->controller = $reflectionClass->newInstanceArgs(array($this->container));
        $this->setViewDefault($app->getCurrentAction());

        return array($this->controller, $this->action, $this->parameters);
    }

    
    public function executeAction(ControllerEvent $controllerEvent)
    {
        $this->controller = $controllerEvent->getController();
        $this->action = $controllerEvent->getAction();

        $controller = new ReflectionObject($this->controller);

        if (($response = $this->executeBeforeFilter($controller)) instanceof Response) {
            return $response;
        }

        if (false === $this->action) {
            return; //si el before devuelve false, es porque no queremos que se ejecute nuestra acción.
        }
        $this->validateAction($controller, $controllerEvent->getParameters());

        $response = call_user_func_array(array($this->controller, $this->action), $controllerEvent->getParameters());

        $this->executeAfterFilter($controller);

        return $response;
    }

    
    public function getPublicProperties()
    {
        return get_object_vars($this->controller);
    }

    
    protected function validateAction(\ReflectionObject $controller, array $params)
    {
        if ($controller->hasProperty('limitParams')) {
            $limitParams = $controller->getProperty('limitParams');
            $limitParams->setAccessible(true);
            $limitParams = $limitParams->getValue($this->controller);
        } else {
            $limitParams = true; //por defeto siempre limita los parametro
        }

        if ($controller->hasProperty('parameters')) {
            $parameters = $controller->getProperty('parameters');
            $parameters->setAccessible(true);
            $parameters->setValue($this->controller, $params);
        }
        //verificamos la existencia del metodo.
        if (!$controller->hasMethod($this->action)) {
            throw new NotFoundException(sprintf("No existe el metodo \"%s\" en el controlador \"%s\"", $this->action, $this->controllerName), 404);
        }

        $reflectionMethod = $controller->getMethod($this->action);

        //el nombre del metodo debe ser exactamente igual al camelCase
        //de la porcion de url
        if ($reflectionMethod->getName() !== $this->action) {
            throw new NotFoundException(sprintf("No existe el metodo <b>%s</b> en el controlador \"%s\"", $this->action, $this->controllerName), 404);
        }

        //se verifica que el metodo sea public
        if (!$reflectionMethod->isPublic()) {
            throw new NotFoundException(sprintf("Éstas Tratando de acceder a un metodo no publico \"%s\" en el controlador \"%s\"", $this->action, $this->controllerName), 404);
        }

        
        if ($limitParams && (count($params) < $reflectionMethod->getNumberOfRequiredParameters() ||
                count($params) > $reflectionMethod->getNumberOfParameters())) {

            throw new NotFoundException(sprintf("Número de parámetros erróneo para ejecutar la acción \"%s\" en el controlador \"%sr\"", $this->action, $this->controllerName), 404);
        }
    }

    
    protected function executeBeforeFilter(ReflectionObject $controller)
    {
        if ($controller->hasMethod('beforeFilter')) {
            $method = $controller->getMethod('beforeFilter');
            $method->setAccessible(true);

            if (null !== $result = $method->invoke($this->controller)) {
                if (false === $result) {
                    //si el resultado es false, es porque no queremos que se ejecute la acción
                    $this->action = false;
                    $this->container->get('app.context')->setCurrentAction(false);
                    return;
                }
                if ($result instanceof Response) {
                    return $result; //devolvemos el objeto Response.
                }
                if (!is_string($result)) {
                    throw new NotFoundException(sprintf("El método \"beforeFilter\" solo puede devolver un <b>false, una cadena, ó un objeto Response<b> en el Controlador \"%s\"", $this->controllerName));
                }
                if (!$controller->hasMethod($result)) {
                    throw new NotFoundException(sprintf("El método \"beforeFilter\" está devolviendo el nombre de una acción inexistente \"%s\" en el Controlador \"%s\"", $result, $this->controllerName));
                }
                //si el beforeFilter del controlador devuelve un valor, el mismo será
                //usado como el nuevo nombre de la acción a ejecutar.
                $this->action = $result;
                $this->container->get('app.context')->setCurrentAction($result);
            }
        }
    }

    
    protected function executeAfterFilter(ReflectionObject $controller)
    {
        if ($controller->hasMethod('afterFilter')) {
            $method = $controller->getMethod('afterFilter');
            $method->setAccessible(true);
            $method->invoke($this->controller);
        }
    }

    
    public function callMethod($method)
    {
        $reflection = new \ReflectionClass($this->controller);

        if ($reflection->hasMethod($method)) {

            //obtengo el parametro del controlador.
            $method = $reflection->getMethod($method);

            //lo hago accesible para poderlo leer
            $method->setAccessible(true);

            //y retorno su valor
            return $method->invoke($this->controller);
        } else {
            return null;
        }
    }

    
    protected function setViewDefault($action)
    {
        $reflection = new \ReflectionClass($this->controller);

        //obtengo el parametro del controlador.
        $propertie = $reflection->getProperty('view');

        //lo hago accesible para poderlo leer
        $propertie->setAccessible(true);
        $propertie->setValue($this->controller, $action);
    }

}

namespace KumbiaPHP\Kernel\Controller;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Router\Router;
use KumbiaPHP\Kernel\Response;


class Controller
{

    
    protected $container;

    
    protected $view;

    
    protected $template = 'default';

    
    protected $response;

    
    protected $cache = null;

    
    protected $limitParams = TRUE;

    
    protected $parameters;

    
    public final function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function renderNotFound($message)
    {
        throw new \KumbiaPHP\Kernel\Exception\NotFoundException($message);
    }

    
    protected function get($id)
    {
        return $this->container->get($id);
    }

    
    protected function getRequest()
    {
        return $this->container->get('request');
    }

    
    protected function getRouter()
    {
        return $this->container->get('router');
    }

    
    protected function setView($view, $template = false)
    {
        $this->view = $view;
        if ($template !== false) {
            $this->setTemplate($template);
        }
    }

    
    protected function setResponse($response, $template = false)
    {
        $this->response = $response;
        if ($template !== false) {
            $this->setTemplate($template);
        }
    }

    
    protected function setTemplate($template)
    {
        $this->template = $template;
    }

    
    protected function getView()
    {
        return $this->view;
    }

    
    protected function getResponse()
    {
        return $this->response;
    }

    
    protected function getTemplate()
    {
        return $this->template;
    }

    
    protected function cache($time = false)
    {
        $this->cache = $time;
    }

    protected function getCache()
    {
        return $this->cache;
    }

    
    protected function render(array $params = array(), $time = null)
    {
        return $this->get('view')->render(array(
                    'template' => $this->getTemplate(),
                    'view' => $this->getView(),
                    'response' => $this->getResponse(),
                    'params' => $params,
                    'time' => $time,
                ));
    }

}

namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Event\RequestEvent;


class ControllerEvent extends RequestEvent
{

    protected $controller = array();

    function __construct(Request $request, array $controller = array())
    {
        parent::__construct($request);
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller[0];
    }

    public function setController($controller)
    {
        $this->controller[0] = $controller;
    }

    public function getAction()
    {
        return $this->controller[1];
    }

    public function setAction($action)
    {
        $this->controller[1] = $action;
    }

    public function getParameters()
    {
        return $this->controller[2];
    }

    public function setParameters(array $parameters)
    {
        $this->controller[2] = $parameters;
    }

}

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Collection;


class Response implements \Serializable
{

    
    public $headers;

    
    protected $content;

    
    protected $statusCode;

    
    protected $charset;

    
    protected $cache;

    
    public function __construct($content = NULL, $statusCode = 200, array $headers = array())
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = new Collection($headers);
        $this->cache = array();
    }

    
    public function getContent()
    {
        return $this->content;
    }

    
    public function setContent($content)
    {
        $this->content = $content;
    }

    
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    
    public function getCharset()
    {
        return $this->charset;
    }

    
    public function setCharset($charset)
    {
        $this->charset = $charset ? : 'UTF-8';
    }

    
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    
    protected function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        if (!$this->headers->has('Content-Type')) {
            $charset = $this->getCharset() ? : 'UTF-8';
            $this->headers->set('Content-Type', "text/html; charset=$charset");
        }

        //mandamos el status
        header(sprintf('HTTP/1.0 %s', $this->statusCode));

        foreach ($this->headers->all() as $index => $value) {
            if (is_string($index)) {
                header("{$index}: {$value}", false);
            } else {
                header("{$value}", false);
            }
        }
    }

    public function serialize()
    {
        return serialize(array(
                    'headers' => $this->headers->all(),
                    'content' => $this->getContent(),
                    'statusCode' => $this->getStatusCode(),
                    'charset' => $this->getCharset(),
                ));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->headers = new Collection($data['headers']);
        $this->setContent($data['content']);
        $this->setStatusCode($data['statusCode']);
        $this->setCharset($data['charset']);
    }

    public function cache($lifetime = NULL, $group = 'default')
    {
        if (NULL !== $lifetime) {
            $this->headers->set('cache-control', 'public');
            $lastModified = new \DateTime();
            $lastModified->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('last-modified', $lastModified->format('D, d M Y H:i:s') . ' GMT');
            $expires = $lastModified->modify($lifetime);
            $this->headers->set('expires', $expires->format('D, d M Y H:i:s') . ' GMT');
            $this->cache = array(
                'time' => $lifetime,
                'group' => $group,
            );
        } else {
            $this->headers->delete('expires');
            $this->cache = array();
        }
    }

    public function getCacheInfo()
    {
        return $this->cache;
    }

    
    protected function sendContent()
    {
        echo $this->content;
        while (ob_get_level()) {
            ob_end_flush(); //vamos limpiando y mostrando todos los niveles de buffer creados.
        }
    }

}

namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;


class ResponseEvent extends RequestEvent
{

    protected $response;

    function __construct(Request $request, Response $response)
    {
        parent::__construct($request);
        $this->response = $response;
    }

    
    public function getResponse()
    {
        return $this->response;
    }

}

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Di\Container\ContainerInterface;


interface KernelInterface
{
    
    const MASTER_REQUEST = 1;
    const SUB_REQUEST = 2;

    
    public function execute(Request $request, $type = KernelInterface::MASTER_REQUEST);
    
    
    public static function get($service);
    
    
    public static function getParam($param);
}


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


abstract class Kernel implements KernelInterface
{

    
    protected $modules;

    
    protected $routes;

    
    protected $di;

    
    protected static $container;

    
    protected $request;

    
    protected $dispatcher;

    
    protected $production;

    
    protected $appPath;

    
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

    
    private function createResponse(ControllerResolver $resolver)
    {
        //como la acción no devolvió respuesta, debemos
        //obtener la vista y el template establecidos en el controlador
        //para pasarlos al servicio view, y este construya la respuesta
        //llamamos al render del servicio "view" y esté nos devolverá
        //una instancia de response con la respuesta creada
        return self::$container->get('view')->render(array(
                    'template' => $resolver->callMethod('getTemplate'),
                    'view' => $resolver->callMethod('getView'),
                    'response' => $resolver->callMethod('getResponse'),
                    'time' => $resolver->callMethod('getCache'),
                    'params' => $resolver->getPublicProperties(), //nos devuelve las propiedades publicas del controlador
                ));
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

    
    abstract protected function registerModules();

    
    abstract protected function registerRoutes();

    
    public function getAppPath()
    {
        if (!$this->appPath) {
            $r = new \ReflectionObject($this);
            $this->appPath = dirname($r->getFileName()) . '/';
        }
        return $this->appPath;
    }

    
    protected function initContainer(array $config = array())
    {
        $definitions = array(
            'services' => $config['services'],
            'parameters' => $config['parameters'],
        );

        $definitions['parameters']['app_dir'] = $this->getAppPath();

        $this->di = new DependencyInjection();

        self::$container = new Container($this->di, $definitions);
    }

    
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

}