<?php

namespace KumbiaPHP\Kernel\Controller;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Router\Router;
use KumbiaPHP\Kernel\Response;

/**
 * Controlador padre de todos los controllers de la aplicación
 *
 * @author manuel
 */
class Controller
{

    /**
     *
     * @var ContainerInterface; 
     */
    protected $container;

    /**
     * Vista a llamar por el servicio de template @view
     * @var string 
     */
    protected $view;

    /**
     * Template a llamar por el servicio de template @template
     * @var string 
     */
    protected $template = 'default';

    /**
     * Tiempo de cacheado ( debe ser una fecha relativa ).
     * 
     * @var string 
     */
    protected $cache = NULL;

    /**
     * indica si se deben limitar el numero de parametros en las acciones ó no.
     * @var boolean 
     */
    protected $limitParams = TRUE;

    /**
     * parametros de la url
     * @var array 
     */
    protected $parameters;

    /**
     * Constructor de la clase
     * @param ContainerInterface $container
     */
    public final function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function renderNotFound($message)
    {
        throw new \KumbiaPHP\Kernel\Exception\NotFoundException($message);
    }

    /**
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Devuelve el objeto resquest de la petición
     * @return Request 
     */
    protected function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * Devuelve el servicio router
     * @return Router 
     */
    protected function getRouter()
    {
        return $this->container->get('router');
    }

    /**
     * Establece la vista a usar
     * @param string $view
     * @param string $template 
     */
    protected function setView($view, $template = FALSE)
    {
        $this->view = $view;
        if ($template !== FALSE) {
            $this->setTemplate($template);
        }
    }

    /**
     * Establece el template a usar
     * @param string $template 
     */
    protected function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * devuelve la vista a mostrar
     * @return string 
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * devuelve el template a mostarr
     * @return string 
     */
    protected function getTemplate()
    {
        return $this->template;
    }

    /**
     * Especifica un tiempo de cache para la vista.
     * 
     * Debe ser una cadena que represente un formato de fecha relativa.
     * 
     * @example $this->cache("+2 days");
     * @example $this->cache("+3 hours");
     * @example $this->cache("+10 sec");
     * 
     * @param string $time
     */
    protected function cache($time = FALSE)
    {
        $this->cache = $time;
    }

    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * Sirve para llamar al servicio de template "view" pasandole
     * unos parametros  y especificando el tiempo de cache.
     * 
     * @param array $params
     * @param type $time
     * @return Response 
     */
    protected function render(array $params = array(), $time = NULL)
    {
        return $this->get('view')->render($this->getTemplate(), $this->getView(), $params, $time);
    }

}