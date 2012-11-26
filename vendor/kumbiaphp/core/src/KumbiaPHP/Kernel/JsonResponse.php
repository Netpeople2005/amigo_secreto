<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Response;

/**
 * Clase que representa una respuesta en formato JSON.
 *
 * @author manuel
 */
class JsonResponse extends Response
{

    /**
     * Constructor de la clase
     * @param string $content contenido para la respuesta
     * @param int $statusCode numero del estado de la respuesta
     * @param array $headers cabeceras para la respuesta
     */
    public function __construct(array $data = array(), $statusCode = 200, array $headers = array())
    {
        parent::__construct(json_encode($data), $statusCode, $headers);

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'application/json');
        }
    }

}