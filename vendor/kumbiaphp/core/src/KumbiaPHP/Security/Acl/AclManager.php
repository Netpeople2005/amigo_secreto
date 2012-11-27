<?php

namespace KumbiaPHP\Security\Acl;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Security\Acl\Acl;
use KumbiaPHP\Kernel\Config\Reader;
use KumbiaPHP\Security\Exception\AclException;
use KumbiaPHP\Security\Acl\Role\RoleInterface;
use KumbiaPHP\Security\Auth\Token\TokenInterface;

/**
 * Description of AclManager
 *
 * @author manuel
 */
class AclManager
{

    /**
     * 
     * @var Acl 
     */
    protected static $acl;
    protected static $resources;

    protected static function createPermisions(TokenInterface $token)
    {
        self::$acl = Acl::factory();

        $roles = $token->getRoles();

        foreach ($roles as $role) {
            if ($role instanceof RoleInterface) {
                $resources = $role->getResources();
                self::$acl->allow($role, $resources);
            } else {
                self::$acl->allow($role, self::getResources($role));
            }
        }

        self::$acl->user($token->getUser(), $roles);
    }

    public static function check(TokenInterface $token, Request $request)
    {
        $url = trim($request->getRequestUrl(), '/');

        return self::checkRoute($token, $url);
    }

    public static function checkRoute(TokenInterface $token, $route)
    {
        if (!self::$acl) {
            self::createPermisions($token);
        }

        return self::$acl->check($token->getUser(), $route);
    }

    protected static function getResources($role = NULL)
    {
        if (!self::$resources) {
            foreach ((array) Reader::get('security.routes') as $route => $roles) {
                foreach (explode(',', $roles) as $r) {
                    self::$resources[trim($r)][] = $route;
                }
            }
        }
        if (NULL === $role) {
            return self::$resources;
        } else {
            //aqui buscamos los recursos para un rol
            return isset(self::$resources[$role]) ? self::$resources[$role] : array();
        }
    }

}