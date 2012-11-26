<?php

namespace KumbiaPHP\Security\Auth\Login;

use KumbiaPHP\Security\Config\Reader;
use KumbiaPHP\Security\Auth\Login\AbstractLogin;

/**
 * Description of Form
 *
 * @author manuel
 */
class Form extends AbstractLogin
{

    public function showLogin()
    {
        $currentUrl = $this->container->get('request')->getRequestUrl();
        $login_url = Reader::get('security.login_url');
        if ($currentUrl !== $login_url) {
            $this->container->get('session')
                    ->set('target_login', $currentUrl, 'security');
        }
        return $this->container->get('router')->redirect($login_url);
    }

}