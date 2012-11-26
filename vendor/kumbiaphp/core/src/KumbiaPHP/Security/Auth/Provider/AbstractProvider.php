<?php

namespace KumbiaPHP\Security\Auth\Provider;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Security\Auth\Provider\UserProviderInterface;

/**
 * Description of Memory
 *
 * @author manuel
 */
abstract class AbstractProvider implements UserProviderInterface
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

}