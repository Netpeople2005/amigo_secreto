<?php

namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\Event;
use KumbiaPHP\EventDispatcher\EventSubscriberInterface;

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
     * @param int $priority orden de prioridad en que se ejecuta el escucha
     */
    public function addListener($eventName, $listener, $priority = 0);

    /**
     * Agrega un subcriptor de eventos al dispatcher
     * @param EventSubscriberInterface $subscriber objeto subscritor
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);

    /**
     * Verifica la existencia de escuchas para un evento
     * @param string $eventName nombre del evento 
     */
    public function hasListeners($eventName);

    /**
     * remueve un escucha del despachador de eventos
     * @param string $eventName nombre del evento 
     * @param string $listener nombre del servicio
     */
    public function removeListener($eventName, $listener);

    public function getListeners($eventName);
}