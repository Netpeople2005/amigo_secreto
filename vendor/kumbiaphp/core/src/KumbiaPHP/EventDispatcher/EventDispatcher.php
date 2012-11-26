<?php

namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\EventDispatcherInterface;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 * Clase que se encarga de despachar los eventos basicos del fw.
 *
 * @author manuel
 */
class EventDispatcher implements EventDispatcherInterface
{

    /**
     * Arreglo que contiene los escuchas insartados en el despachador.
     *
     * @var array 
     */
    protected $listeners = array();

    /**
     * Clase container para obtener las instancias de los servicios
     * que escuchan eventos, para llamarlos el despachar un evento.
     *
     * @var ContainerInterface 
     */
    protected $container;

    /**
     * Constructor de la clase.
     * @param ContainerInterface $container 
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function dispatch($eventName, Event $event)
    {
        if (!array_key_exists($eventName, $this->listeners)) {
            return;
        }
        if (is_array($this->listeners[$eventName]) && count($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $service = $this->container->get($listener[0]);
                $service->{$listener[1]}($event);
                if ($event->isPropagationStopped()) {
                    return;
                }
            }
        }
    }

    public function addListener($eventName, $listener)
    {
        if (!$this->hasListener($eventName, $listener)) {
            $this->listeners[$eventName][] = $listener;
        }
    }

    public function hasListener($eventName, $listener)
    {
        if (isset($this->listeners[$eventName])) {
            return in_array($listener, $this->listeners[$eventName]);
        } else {
            return FALSE;
        }
    }

    public function removeListener($eventName, $listener)
    {
        if ($this->hasListener($eventName, $listener)) {
            do {
                if ($listener === current($this->listeners[$eventName])) {
                    $key = key(current($this->listeners[$eventName]));
                    break;
                }
            } while (next($this->listeners[$eventName]));
        }
        unset($this->listeners[$eventName][$key]);
    }

}