<?php

namespace KumbiaPHP\Security\Event;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Security\Security;

class SecurityEvent extends RequestEvent
{

    /**
     *
     * @var Security 
     */
    protected $security;

    function __construct(Request $request, Security $security)
    {
        parent::__construct($request);
        $this->security = $security;
    }

    /**
     * @return Security
     */
    public function getSecutiry()
    {
        return $this->security;
    }

}
