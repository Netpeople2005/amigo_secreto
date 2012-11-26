<?php

namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\EventDispatcher\Event;

/**
 * Description of RequestEvent
 *
 * @author manuel
 */
class RequestEvent extends Event
{

    /**
     *
     * @var Request 
     */
    protected $request;

    /**
     *
     * @var Response 
     */
    protected $response;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Devuelve el objeto Request
     * @return Request 
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Verifica si se ha establecido una respuesta en la clase.
     * @return boolean 
     */
    public function hasResponse()
    {
        return $this->response instanceof Response;
    }

    /**
     * Devuelve la respuesta contenida en la clase
     * 
     * @return Response 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Establece una respuesta para la clase.
     * 
     * @param Response $response 
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

}