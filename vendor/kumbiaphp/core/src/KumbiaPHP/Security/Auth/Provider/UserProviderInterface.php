<?php

namespace KumbiaPHP\Security\Auth\Provider;

use KumbiaPHP\Security\Auth\Token\TokenInterface;
use KumbiaPHP\Security\Auth\User\UserInterface;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 *
 * @author manuel
 */
interface UserProviderInterface
{

    public function __construct(ContainerInterface $container);
    /**
     *  @return UserInterface
     */
    public function loadUser(TokenInterface $token);
    public function getToken(array $config = array());
}
