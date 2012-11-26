<?php

namespace KumbiaPHP\Security\Acl\Adapter;

use KumbiaPHP\Security\Acl\Acl;
use KumbiaPHP\Security\Exception\AclException;
use KumbiaPHP\Security\Auth\User\UserInterface;

/**
 * Implementacion de ACL con definicion de reglas en PHP
 *
 * @category   Kumbia
 * @package    Acl
 */
class Simple extends Acl
{

    protected $roles;
    protected $users;

    public function allow($role, array $resources = array())
    {
        foreach ($resources as $resource) {
            $this->roles[$this->getRole($role)]['resources'][] = '/' . trim($this->getResource($resource), '/');
        }

        return $this;
    }

    public function parents($role, array $parents = array())
    {
        $this->roles[$this->getRole($role)]['parents'] = $parents;
        return $this;
    }

    public function user(UserInterface $user, array $roles = array())
    {
        $this->users[$user->getUsername()] = count($roles) ? $roles : $user->getRoles();
        return $this;
    }

}
