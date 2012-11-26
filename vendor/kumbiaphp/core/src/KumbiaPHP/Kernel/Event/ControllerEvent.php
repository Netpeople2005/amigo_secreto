<?php

namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Event\RequestEvent;

/**
 * Description of ControllerEvent
 *
 * @author manuel
 */
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