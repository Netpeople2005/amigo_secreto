<?php

namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\Event;

/**
 * Esta interface define métodos que deberán ser implementados por los 
 * despachadores de eventos en el FW.
 *
 * @author manuel
 */
interface EventDispatcherInterface
{

    /**
     * Dispara los escuchas para un evento particular.
     * @param string $eventName nombre del evento a disparar
     * @param Event clase a pasar a los escuchas, para cada evento pueden ser clases especificas.
     */
    public function dispatch($eventName, Event $event);

    /**
     * Agrega un escucha al despachador de eventos
     * @param string $eventName nombre del evento a disparar
     * @param array $listener un arreglo con el nombre del servicio y el metodo a llamar
     */
    public function addListener($eventName, $listener);

    /**
     * Verifica la existencia de un escucha en el despachador de eventos
     * @param string $eventName nombre del evento 
     * @param string $listener nombre del servicio
     */
    public function hasListener($eventName, $listener);

    /**
     * remueve un escucha del despachador de eventos
     * @param string $eventName nombre del evento 
     * @param string $listener nombre del servicio
     */
    public function removeListener($eventName, $listener);
}