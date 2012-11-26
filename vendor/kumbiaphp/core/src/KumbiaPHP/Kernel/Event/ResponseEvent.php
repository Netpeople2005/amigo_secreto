<?php

namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;

/**
 * Description of ResponseEvent
 *
 * @author manuel
 */
class ResponseEvent extends RequestEvent
{

    protected $response;

    function __construct(Request $request, Response $response)
    {
        parent::__construct($request);
        $this->response = $response;
    }

    /**
     *
     * @return Response 
     */
    public function getResponse()
    {
        return $this->response;
    }

}