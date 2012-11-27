<?php

namespace KumbiaPHP\Security\Acl;

use KumbiaPHP\Security\Acl\AclInterface;
use KumbiaPHP\Security\Acl\Role\RoleInterface;
use KumbiaPHP\Security\Exception\AclException;
use KumbiaPHP\Security\Auth\User\UserInterface;
use KumbiaPHP\Security\Acl\Resource\ResourceInterface;

/**
 * Clase Base para gestión de ACL
 *
 * Nueva Clase Base para gestión de ACL (Access Control List) permisos
 *
 * @category   Kumbia
 * @package    Acl
 */
abstract class Acl implements AclInterface
{

    /**
     *
     * @param string $adapter
     * @return AclInterface 
     */
    public static function factory($adapter = 'simple')
    {
        return new Adapter\Simple();
    }

    public function check(UserInterface $user, $resource)
    {
        $roles = array_merge(array('default'), (array) $user->getRoles());
        
        foreach ((array) $roles as $role) {
            if ($this->isAllowed($role, $resource)) {
                return true;
            }
        }
        return false;
    }

    protected function isAllowed($role, $resource)
    {
        $role = $this->getRole($role);
        $resource = '/' . trim($this->getResource($resource), '/');

        if (!isset($this->roles[$role])) {
            return false;
        }

        if (in_array($resource, $this->roles[$role]['resources'])) {
            return true;
        }

        foreach ((array) $this->roles[$role]['resources'] as $res) {

            if (false !== strripos($res, '*', -1)) {
                $res = rtrim($res, '/*');
                if (0 === strpos($resource, $res)) {
                    return true;
                }
            } elseif ($res === $resource) {
                return true;
            }
        }

        if (!isset($this->roles[$role]['parents'])) {
            return false;
        }

        foreach ((array) $this->roles[$role]['parents'] as $parent) {
            if ($this->isAllowed($parent, $resource)) {
                return true;
            }
        }
        return false;
    }

    protected function getRole($role)
    {
        if ($role instanceof RoleInterface) {
            $role = $role->getName();
        } elseif (!is_string($role) && !is_int($role)) {
            throw new AclException('el parametro $role debe ser una cadena ó un objeto de tipo RoleInterface');
        }
        return $role;
    }

    protected function getResource($resource)
    {
        if ($resource instanceof ResourceInterface) {
            $resource = $resource->getName();
        } elseif (!is_string($resource) && !is_int($resource)) {
            throw new AclException('el parametro $resource debe ser una cadena ó un objeto de tipo RoleInterface');
        }
        return $resource;
    }

}
