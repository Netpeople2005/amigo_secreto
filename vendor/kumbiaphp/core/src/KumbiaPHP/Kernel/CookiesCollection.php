<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Collection;

class CookiesCollection extends Collection
{

    /**
     * Verifica la existencia de una cookie
     * @param string $key
     * @return boolean 
     */
    public function has($key)
    {
        return array_key_exists($key, $_COOKIE);
    }

    /**
     * Devuelve el valor de una cookie si existe, si no existe devuelve
     * el valor por defecto asignado en el segundo parametro del metodo.
     * @param string $key nombre de la cookie
     * @param mixed $default valor a retornar si no existe el parametro
     * @return mixed
     */
    public function get($key, $default = NULL)
    {
        return $this->has($key) ? $_COOKIE[$key] : $default;
    }

    /**
     * Establece un parametro
     * @param string $key
     * @param mixed $value 
     */
    public function set($key, $value, $expire = 0)
    {
        setcookie($key, $value, $expire);
    }

    /**
     * Devuelve todas las cookies de la clase
     * @return array 
     */
    public function all()
    {
        return (array) $_COOKIE;
    }

    /**
     * Devuelve el numero de cookies contenidas en la clase
     * @return type 
     */
    public function count()
    {
        return count($_COOKIE);
    }

    /**
     * Elimina una cookie
     * @param string $key 
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            $this->set($key, false);
        }
    }

    /**
     * Elimina todos los parametros de la clase 
     */
    public function clear()
    {
        foreach ($this->keys() as $cookie) {
            $this->delete($cookie);
        }
    }

}