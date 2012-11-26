<?php

namespace KumbiaPHP\Security\Auth\Token;

use KumbiaPHP\Security\Acl\Role\RoleInterface;
use KumbiaPHP\Security\Auth\User\UserInterface;
use KumbiaPHP\Security\Auth\Token\TokenInterface;

/**
 * Description of Token
 *
 * @author manuel
 */
abstract class AbstractToken implements TokenInterface
{

    /**
     *  
     * @var UserInterface
     */
    protected $user;
    protected $valid;
    protected $attributes;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUsername()
    {
        return $this->user->getUsername();
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function serialize()
    {
        return serialize(array($this->user, $this->valid));
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function unserialize($serialized)
    {
        list($this->user, $this->valid) = unserialize($serialized);
        if (($this->user instanceof UserInterface) && method_exists($this->user, '__construct')) {
            $this->user->__construct();
        }
    }

    public function setAutenticated($autenticate)
    {
        $this->valid = $autenticate;
    }

    public function getRoles()
    {
        return ($this->user instanceof UserInterface) ? (array) $this->user->getRoles() : array();
    }

    public function getAttributes($attr = NULL)
    {
        if (NULL === $attr) {
            return $this->attributes;
        } else {
            if (($this->user instanceof UserInterface) && isset($this->user->{$attr})) {
                return $this->user->{$attr};
            } else {
                return isset($this->attributes[$attr]) ? $this->attributes[$attr] : NULL;
            }
        }
    }

}