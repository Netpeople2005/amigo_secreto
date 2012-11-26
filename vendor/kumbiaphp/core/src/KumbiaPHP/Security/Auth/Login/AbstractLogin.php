<?php

namespace KumbiaPHP\Security\Auth\Login;

use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 * Description of LoginInterface
 *
 * @author manuel
 */
abstract class AbstractLogin
{

    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return Response 
     */
    abstract public function showLogin();
}