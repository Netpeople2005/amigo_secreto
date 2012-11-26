<?php

namespace KumbiaPHP\Flash;

class FlashCollection
{

    protected $flashes;

    public function __construct(array $flashes = array())
    {
        $this->flashes = $flashes;
    }

    /**
     * Agrega un mensaje flash
     * 
     * @param string $type tipo del mensaje ( success, info , error, advertencia )
     * @param string|array $message  el mensaje a guardar.
     */
    public function add($type, $message)
    {
        $type = trim($type);
        if (!isset($this->flashes[$type])) {
            $this->flashes[$type] = array();
        }
        $this->flashes[$type] = array_merge($this->flashes[$type],(array) $message);
    }

    /**
     * Verifica la existencia de un mensaje en la clase, se debe pasar su tipo
     * @param string $type
     * @return boolean 
     */
    public function has($type)
    {
        $type = trim($type);
        return isset($this->flashes[$type]);
    }

    /**
     * Devuelve los mensajes que han sido previamente guardados, si existen.
     * 
     * antes de devolverlos, son borrados de la sesión.
     * 
     * @param string $type
     * @return array|NULL 
     */
    public function get($type)
    {
        $type = trim($type);
        if ($this->has($type)) {
            $messages = $this->flashes[$type];
            unset($this->flashes[$type]);
            return $messages;
        } else {
            return NULL;
        }
    }

    /**
     * Devuelve todos los mensajes guardados previamente y los borra
     * de la session.
     * 
     * @return array arreglo donde los indices son el tipo de mensaje y el valor
     * es el contenido del mensaje. 
     */
    public function all()
    {
        $messages = $this->flashes;
        $this->flashes = array();
        return $messages;
    }

}
