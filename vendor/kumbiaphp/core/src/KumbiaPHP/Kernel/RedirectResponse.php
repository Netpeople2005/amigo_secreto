<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Response;

/**
 * Respuesta para hacer redirecciones
 *
 * @author maguirre
 */
class RedirectResponse extends Response
{

    /**
     * Url a la que se va a redireccionar
     * @param string $url
     * @param int $status 
     */
    public function __construct($url, $status = 302)
    {
        parent::__construct(sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="refresh" content="1;url=%1$s" />

        <title>Redireccionando a %1$s</title>
    </head>
    <body>
        Redireccionando a <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')), $status, array(
            'Location' => $url
        ));
    }

}
