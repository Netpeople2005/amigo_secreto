<?php

namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Kernel\Event\RequestEvent;

/**
 * Description of RequestEvent
 *
 * @author manuel
 */
class ExceptionEvent extends RequestEvent
{

    /**
     *
     * @var \Exception 
     */
    protected $exception;

    public function __construct(\Exception $e, Request $request)
    {
        parent::__construct($request);
        $this->exception = $e;
    }
    
    /**
     * Devuelve la Excepción que se disparó
     * @return \Exception 
     */
    public function getException()
    {
        return $this->exception;
    }
}