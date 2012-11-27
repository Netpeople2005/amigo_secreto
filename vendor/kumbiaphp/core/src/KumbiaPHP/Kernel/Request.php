<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Collection;
use KumbiaPHP\Kernel\AppContext;
use KumbiaPHP\Kernel\FilesCollection;
use KumbiaPHP\Kernel\CookiesCollection;
use KumbiaPHP\Kernel\Session\SessionInterface;

/**
 * Esta clase representa una petición HTTP.
 *
 * @author manuel
 */
class Request
{

    /**
     * Contiene la Informaci�n de la variable $_SERVER
     * @var Collection
     */
    public $server;

    /**
     * Contiene la Informaci�n de la variable $_REQUEST
     * @var Collection
     */
    public $request;

    /**
     * Contiene la Informaci�n de la variable $_GET
     * @var Collection 
     */
    public $query;

    /**
     * Contiene la informaci�n de la variable $_COOKIE
     * @var Collection 
     */
    public $cookies;

    /**
     * Contiene la informaci�n de la variable $_FILES
     * @var Collection 
     */
    public $files;

    /**
     * Contiene el contexto de ejecución de la aplicación
     * @var AppContext
     */
    protected $app;

    /**
     *
     * @var string 
     */
    private $baseUrl;

    /**
     * Contenido del Request Body de la petición
     * @var string 
     */
    protected $content = FALSE;
    protected $locale;

    /**
     * Constructor de la clase. 
     * Rellena los parametros de la clase con la info de la petición
     * los valores de las variables globales $_SERVER, $_POST, $_GET , $_COOKIE
     * $_FILES pasan a estár contenidos en atributos de la clase con el fin
     * de ofrecer una arquitectura de comunicacion con estos valores orientada 
     * a objetos. 
     */
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

    /**
     * Devuelve el valor para un indice de las variables globales de la petición
     * 
     * Primero busca en request, sino lo encuentra busca en query y si no existe
     * lo busca en las cookies.
     * 
     * De no exitir en ninguna de las variables devuelve el valor por defecto.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed 
     */
    public function get($key, $default = NULL)
    {
        //busca en request, si no existe busca en query sino existe busca en 
        //cookies, si no devuelve $default.
        return $this->request->get($key, $this->query->get($key, $this->cookies->get($key, $default)));
    }

    /**
     * Devuelve la instancia del objeto que tiene el contexto de la aplicación
     * @return AppContext
     */
    public function getAppContext()
    {
        return $this->app;
    }

    /**
     * Estabelce la instancia del objeto que tiene el contexto de la aplicación
     * @param SessionInterface $session 
     */
    public function setAppContext(AppContext $app)
    {
        $this->app = $app;
    }

    /**
     * Devuelve el metodo de la petición
     * @return string 
     */
    public function getMethod()
    {
        return $this->server->get('REQUEST_METHOD', 'GET');
    }

    /**
     * Devuelve la IP del cliente
     * @return string 
     */
    public function getClientIp()
    {
        return $this->server->get('REMOTE_ADDR');
    }

    /**
     * Devuelve TRUE si la petición es Ajax
     * @return boolean 
     */
    public function isAjax()
    {
        return $this->server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    /**
     * Devuelve TRUE si el metodo de la petición es el pasado por parametro
     * @param string $method
     * @return boolean 
     */
    public function isMethod($method)
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }

    /**
     * Devuelve el url base del proyecto
     * @return string 
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Devuelve la url de la petición actual
     * @return type 
     */
    public function getRequestUrl()
    {
        return $this->query->get('_url', '/');
    }

    /**
     * Devuelve el Cuerpo de la petición
     * @return string 
     */
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

    /**
     * Obtiene el Locale del Petición.
     * @return type 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Establece el Locale de la Petición.
     * @param string $locale 
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Crea la url base de la petición.
     * @return string 
     */
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