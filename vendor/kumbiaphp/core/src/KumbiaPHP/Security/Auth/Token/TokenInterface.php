<?php

namespace KumbiaPHP\Security\Auth\Token;

use \Serializable;
use KumbiaPHP\Security\Auth\User\UserInterface;

/**
 *
 * @author manuel
 */
interface TokenInterface extends Serializable
{
    public function getUser();

    public function getUsername();

    public function getRoles();
    
    public function getAttributes($attr = NULL);

    public function setUser($user);

    public function setAutenticated($autenticate);

    public function isValid();
}
