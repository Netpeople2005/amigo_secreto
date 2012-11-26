<?php

namespace KumbiaPHP\Flash;

use KumbiaPHP\Flash\FlashCollection;
use KumbiaPHP\Kernel\Session\SessionInterface;

/**
 * Clase que permite el envio de mensajes flash desde un controlador,
 * para luego ser leido en las vistas.
 * 
 * Cada vez que leemos los mensajes que han sido previamente guardados,
 * estos son borrados de la sesión, para que solo nos aparescan una vez.
 *
 * @author manuel
 */
class Flash
{

    /**
     * Contiene los mensajes que se van enviando.
     *
     * @var FlashCollection 
     */
    private $messages;

    /**
     * Constructor de la clase, usa la clase sesion para guardar los mensajes
     * 
     * @param SessionInterface $session 
     */
    public function __construct(SessionInterface $session)
    {
        //si no existe el indice en la sesión, lo creamos.
        if (!$session->has('messages', 'flash')) {
            $session->set('messages', new FlashCollection(), 'flash');
        }
        //le pasamos el objeto parameters
        $this->messages = $session->get('messages', 'flash');
    }

    /**
     * Establece un mensaje flash
     * 
     * @param string $type tipo del mensaje ( success, info , error, advertencia )
     * @param string|array $message  el mensaje a guardar.
     */
    public function set($type, $message)
    {
        $this->messages->add($type, $message);
    }

    /**
     * Verifica la existencia de un mensaje en la clase, se debe pasar su tipo
     * @param string $type
     * @return boolean 
     */
    public function has($type)
    {
        return $this->messages->has($type);
    }

    /**
     * Devuelve los mensajes que han sido previamente guardados para un tipo especifico, si existen.
     * 
     * antes de devolverlos, son borrados de la sesión.
     * 
     * @param string $type
     * @return array|NULL 
     */
    public function get($type)
    {
        return $this->messages->get($type);
    }

    /**
     * Devuelve todos los mensajes guardados previamente y los borra
     * de la session.
     * 
     * @return array arreglo donde los indices son el tipo de mensaje y el valor
     * es el contenido del mensaje. 
     */
    public function getAll()
    {
        return $this->messages->all();
    }

    /**
     * Establece un mensaje de tipo success
     * @param string|array $message 
     */
    public function success($message)
    {
        $this->set('success', $message);
    }

    /**
     * Establece un mensaje de tipo info
     * @param string|array $message 
     */
    public function info($message)
    {
        $this->set('info', $message);
    }

    /**
     * Establece un mensaje de tipo warning
     * @param string|array $message 
     */
    public function warning($message)
    {
        $this->set('warning', $message);
    }

    /**
     * Establece un mensaje de tipo error
     * @param string|array $message 
     */
    public function error($message)
    {
        $this->set('error', $message);
    }
}