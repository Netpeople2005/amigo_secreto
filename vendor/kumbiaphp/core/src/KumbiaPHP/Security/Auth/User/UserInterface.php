<?php

namespace KumbiaPHP\Security\Auth\User;

/**
 * Description of UserInterface
 *
 * @author manuel
 */
interface UserInterface
{

    public function getUsername();

    public function getPassword();

    public function auth(UserInterface $user);

    public function getRoles();
}