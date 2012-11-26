<?php

namespace KumbiaPHP\Security\Acl;

use KumbiaPHP\Security\Acl\Role\RoleInterface;
use KumbiaPHP\Security\Auth\User\UserInterface;
use KumbiaPHP\Security\Acl\Resource\ResourceInterface;

/**
 *
 * @author maguirre
 */
interface AclInterface
{

    /**
     * Establece los recursos a los que el rol puede acceder
     *
     * @param RoleInterface|string $role nombre de rol
     * @param array $resources recursos a los que puede acceder el rol
     * @return AclInterface
     */
    public function allow($role, array $resources = array());

    /**
     * Establece los padres del rol
     *
     * @param RoleInterface|string $role rol
     * @param array $parents padres del rol
     * @return AclInterface
     */
    public function parents($role, array $parents = array());

    /**
     * Adiciona un usuario a la lista
     *
     * @param UserInterface $user
     * @param array $roles si se especifica, estos serán los roles del usuario
     * y no se llamara al metodo getRoles del objeto $user
     * @return AclInterface
     */
    public function user(UserInterface $user, array $roles = array());

    /**
     * Verifica si el usuario puede acceder al recurso
     * 
     * @param UserInterface $user usuario de la acl
     * @param ResourceInterface|string $resource recurso al cual se verificará acceso
     * @return boolean
     */
    public function check(UserInterface $user, $resource);
}
