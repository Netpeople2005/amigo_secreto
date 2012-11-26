<?php

namespace KumbiaPHP\Security\Auth\Login;

use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Security\Auth\Login\AbstractLogin;

/**
 * Description of Http
 *
 * @author manuel
 */
class Http extends AbstractLogin
{

    //put your code here
    public function showLogin()
    {
        return new Response(NULL, 401, array(
                    'WWW-Authenticate' => 'Basic realm=""',
                    'HTTP/1.0 401 Unauthorized',
                ));
    }

}