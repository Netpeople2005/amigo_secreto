<?php

namespace KumbiaPHP\Kernel;

/**
 * Contiene parametros de cualquier tipo. ademas ofrece métodos
 * para obtener dichos parametros filtrados.
 *
 * @author manuel
 */
class Collection implements \Serializable
{

    /**
     * Arreglo con los valores
     * @var array 
     */
    protected $params;

    /**
     * Constructor de la clase
     * @param array $params parametros iniciales
     */
    function __construct(array $params = array())
    {
        $this->params = $params;
    }

    /**
     * Verifica la existencia de un parametro
     * @param string $key
     * @return boolean 
     */
    public function has($key)
    {
        return array_key_exists($key, $this->params);
    }

    /**
     * Devuelve el valor de un parametro si existe, si no existe devuelve
     * el valor por defecto asignado en el segundo parametro del metodo.
     * @param string $key nombre del parametro
     * @param mixed $default valor a retornar si no existe el parametro
     * @return mixed
     */
    public function get($key, $default = NULL)
    {
        return $this->has($key) ? $this->params[$key] : $default;
    }

    /**
     * Establece un parametro
     * @param string $key
     * @param mixed $value 
     */
    public function set($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * Devuelve todos los parametros de la clase
     * @return array 
     */
    public function all()
    {
        return $this->params;
    }

    /**
     * Devuelve el numero de parametros contenidos en la clase
     * @return type 
     */
    public function count()
    {
        return count($this->params);
    }

    /**
     * Elimina un parametro
     * @param string $key 
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            unset($this->params[$key]);
        }
    }

    /**
     * elimina todos los parametros de la clase 
     */
    public function clear()
    {
        $this->params = array();
    }

    /**
     * implementacion del metodo de la interface Serializable
     * @return string 
     */
    public function serialize()
    {
        return serialize($this->params);
    }

    /**
     * implementacion del metodo de la interface Serializable
     * @param string $serialized 
     */
    public function unserialize($serialized)
    {
        $this->params = unserialize($serialized);
    }

    /**
     * Devuelve un arreglo con todos los indices de los parametros que 
     * contiene la clase
     * @return array 
     */
    public function keys()
    {
        return array_keys($this->all());
    }

    /**
     * Devuelve un parametro convertido a entero, ó el valor por defecto
     * si no existe
     * @param string $key
     * @param int $default
     * @return int 
     */
    public function getInt($key, $default = 0)
    {
        return (int) $this->get($key, $default);
    }

    /**
     * Devuelve los digitos contenidos en un parametro, ó los digitos deel 
     * valor por defecto si no existe
     * @param string $key
     * @param mixed $default
     * @return int 
     */
    public function getDigits($key, $default = '')
    {
        return preg_replace('/[^[:digit:]]/', '', $this->get($key, $default));
    }

    /**
     * @todo pendiente con getAlnum, no funciona bien por el momento.
     * Devuelve los caracteres alfanumericos de un parametro, ó
     * los caracteres alfanumericos del valor por defecto si no existe el param.
     * @param string $key
     * @param string $default
     * @return string 
     * 
     */
    public function getAlnum($key, $default = '')
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }

    /**
     * Devuelve los caracteres que solo sean letras de un parametro si existe,
     * sino devuelve los caracteres que solo sean letras del valor por defecto.
     * @param string $key
     * @param string $default
     * @return string 
     */
    public function getAlpha($key, $default = '')
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }

}