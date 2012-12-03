<?php

namespace KumbiaPHP\EventDispatcher;

/**
 * Description of EventSubcriberInterface
 *
 * @author manuel
 */
class EventSubscriberInterface
{

    /** 
     * Devuelve un arreglo con los metodos a llamar en los eventos establecidos.
     * 
     * @example
     * 
     *      return array(
     *          'onRequest' => 'kumbia.request',
     *          'onResponse' => 'kumbia.response',
     *          'respuesta2' => 'kumbia.response',
     *          'onBeforeQuery' => 'activerecord.beforequery',
     *          'onLoginSuccess' => 'security.login',
     *      );
     */
    public function getSubscribedEvents();
}