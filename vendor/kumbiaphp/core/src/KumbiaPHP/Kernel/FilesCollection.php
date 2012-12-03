<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\File;

class FilesCollection
{

    /**
     * Arreglo con los File
     * @var array
     */
    protected $params;

    public function __construct()
    {
        foreach ((array) $_FILES as $name => $file) {
            if (isset($file['name']) && is_array($file['name'])) {
                foreach (array_keys($file['name']) as $key) {
                    $this->set($key, new File(array(
                            'name' => $file['name'][$key],
                            'type' => $file['type'][$key],
                            'tmp_name' => $file['tmp_name'][$key],
                            'error' => $file['error'][$key],
                            'size' => $file['size'][$key],
                        )), $name);
                }
            } else {
                $this->set($name, new File($file));
            }
        }
    }

    /**
     * Verifica la existencia de un parametro
     * @param string $key
     * @param string|null $form formulario al que pertenece [opcional]
     * @return boolean
     */
    public function has($key, $form = null)
    {
        if (null === $form) {
            return array_key_exists($key, $this->params);
        } else {
            return isset($this->params[$form]) && isset($this->params[$form][$key]);
        }
    }

    /**
     * Devuelve el valor de un parametro si existe.
     * @param string $key nombre del parametro
     * @param string|null $form formulario al que pertenece [opcional]
     * @return mixed
     */
    public function get($key, $form = null)
    {
        if (null === $form && $this->has($key)) {
            return $this->params[$key];
        } elseif ($this->has($key, $form)) {
            return $this->params[$form][$key];
        } else {
            return null;
        }
    }

    /**
     * Establece un parametro
     * @param string $key
     * @param File $file
     * @param string|null $form formulario al que pertenece [opcional]
     */
    public function set($key, File $file, $form = null)
    {
        if (null === $form) {
            $this->params[$key] = $file;
        } else {
            $this->params[$form][$key] = $file;
        }
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
     * @param string|null $form formulario al que pertenece [opcional]
     */
    public function delete($key, $form = null)
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
     * Devuelve un arreglo con todos los indices de los parametros que
     * contiene la clase
     * @return array
     */
    public function keys()
    {
        return array_keys($this->all());
    }

}