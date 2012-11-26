<?php

namespace KumbiaPHP\Security\Auth\User;

use KumbiaPHP\Security\Config\Reader;
use KumbiaPHP\Security\Auth\User\User;

/**
 * Description of Memory
 *
 * @author manuel
 */
class Memory extends User
{

    public function auth(UserInterface $user)
    {
        return (string) $this->password === (string) $user->getPassword() && parent::auth($user);
    }

}