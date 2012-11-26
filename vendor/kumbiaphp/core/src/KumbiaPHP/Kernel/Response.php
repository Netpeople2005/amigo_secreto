<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Collection;

/**
 * Clase que representa la respuesta de la petición.
 *
 * @author manuel
 */
class Response implements \Serializable
{

    /**
     * Cabeceras para la respuesta
     * @var Collection 
     */
    public $headers;

    /**
     * Contenido a mostrar en la respuesta
     * @var string 
     */
    protected $content;

    /**
     * Numero de estado de la respuesta.
     * @var int 
     */
    protected $statusCode;

    /**
     * codificación del contenido de la respuesta
     * @var string 
     */
    protected $charset;

    /**
     * Información para la cache.
     * @var array 
     */
    protected $cache;

    /**
     * Constructor de la clase
     * @param string $content contenido para la respuesta
     * @param int $statusCode numero del estado de la respuesta
     * @param array $headers cabeceras para la respuesta
     */
    public function __construct($content = NULL, $statusCode = 200, array $headers = array())
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = new Collection($headers);
        $this->cache = array();
    }

    /**
     * Devuelve el contenido de la respuesta
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * establece el contenido para la respuesta
     * @param string $content 
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * devuelve el numero de estado de la respuesta
     * @return int 
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Establece el numero de estado de la respuesta
     * @param int $statusCode 
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * devuelve el charset de la respuesta
     * @return string 
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * establece el charset para la respuesta
     * @param string $charset 
     */
    public function setCharset($charset)
    {
        $this->charset = $charset ? : 'UTF-8';
    }

    /**
     * Envia la respuesta 
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Envia las cabeceras si estas no fueron enviadas ya antes.
     */
    protected function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        if (!$this->headers->has('Content-Type')) {
            $charset = $this->getCharset() ? : 'UTF-8';
            $this->headers->set('Content-Type', "text/html; charset=$charset");
        }

        //mandamos el status
        header(sprintf('HTTP/1.0 %s', $this->statusCode));

        foreach ($this->headers->all() as $index => $value) {
            if (is_string($index)) {
                header("{$index}: {$value}", false);
            } else {
                header("{$value}", false);
            }
        }
    }

    public function serialize()
    {
        return serialize(array(
                    'headers' => $this->headers->all(),
                    'content' => $this->getContent(),
                    'statusCode' => $this->getStatusCode(),
                    'charset' => $this->getCharset(),
                ));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->headers = new Collection($data['headers']);
        $this->setContent($data['content']);
        $this->setStatusCode($data['statusCode']);
        $this->setCharset($data['charset']);
    }

    public function cache($lifetime = NULL, $group = 'default')
    {
        if (NULL !== $lifetime) {
            $this->headers->set('cache-control', 'public');
            $lastModified = new \DateTime();
            $lastModified->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('last-modified', $lastModified->format('D, d M Y H:i:s') . ' GMT');
            $expires = $lastModified->modify($lifetime);
            $this->headers->set('expires', $expires->format('D, d M Y H:i:s') . ' GMT');
            $this->cache = array(
                'time' => $lifetime,
                'group' => $group,
            );
        } else {
            $this->headers->delete('expires');
            $this->cache = array();
        }
    }

    public function getCacheInfo()
    {
        return $this->cache;
    }

    /**
     * imprime el contenido de la respuesta.
     */
    protected function sendContent()
    {
        echo $this->content;
        while (ob_get_level()) {
            ob_end_flush(); //vamos limpiando y mostrando todos los niveles de buffer creados.
        }
    }

}