<?php

namespace KumbiaPHP\Security;

use KumbiaPHP\Security\Acl\AclManager;
use KumbiaPHP\Kernel\Session\SessionInterface;
use KumbiaPHP\Security\Auth\Token\TokenInterface;

/**
 * Description of Security
 *
 * @author manuel
 */
class Security
{

    /**
     *
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     *
     * @param string $rol
     * @return boolean 
     */
    public function isLogged($rol = NULL)
    {
        if (NULL === $rol) {
            return $this->session->has('token', 'security') &&
                    $this->session->get('token', 'security') instanceof TokenInterface &&
                    $this->getToken()->isValid();
        } else {
            if ($this->session->has('token', 'security')) {
                return $this->getToken()->isValid() && in_array($rol, (array) $this->getToken()->getRoles());
            }
        }
    }

    /**
     *
     * @return TokenInterface 
     */
    public function getToken($attr = NULL)
    {
        if (NULL === $attr) {
            return $this->session->get('token', 'security');
        } else {
            if ($this->session->has('token', 'security')) {
                return $this->session->get('token', 'security')
                                ->getAttributes($attr);
            } else {
                return NULL;
            }
        }
    }

    /**
     * Verifica que el usuario actual tenga permisos para acceder a la 
     * ruta especificada.
     * @param string $route
     * @return boolean|NULL TRUE p FALSE si está logueado, sino devuelve NULL
     */
    public function hasPermissions($route)
    {
        if ($this->isLogged()) {
            return (boolean) AclManager::checkRoute($this->getToken(), $route);
        } else {
            return NULL;
        }
    }

}