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
     * Arreglo con los servicios ya ordenados por prioridad.
     * @var array 
     */
    protected $sorted = array();

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
        if (!$this->hasListeners($eventName)) {
            return;
        }
        foreach ($this->getListeners($eventName) as $listener) {
            call_user_func($listener, $event);
            if ($event->isPropagationStopped()) {
                return;
            }
        }
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    public function hasListeners($eventName)
    {
        return isset($this->listeners[$eventName]);
    }

    public function getListeners($eventName)
    {
        if (isset($this->sorted[$eventName])) {
            //si ya estan ordenados, solo devolvemos los listeners.
            return $this->sorted[$eventName];
        }

        //si no estan en el arreglo $sorted, lo creamos.
        $this->sortListeners($eventName);

        foreach ($this->sorted[$eventName] as $index => $listener) {
            if (!is_callable($listener)) {
                //si listener no es un funcion ó un objeto con un metodo que se pueda llamar
                //es porque estamos solicitando un servicio.
                //entonces convertirmos el listener en un objeto con un metodo que se
                //puedan llamar.
                $service = $this->container->get($listener[0]);
                $this->sorted[$eventName][$index][0] = $service;
            }
        }

        return $this->sorted[$eventName];
    }

    public function removeListener($eventName, $listener)
    {
        if ($this->hasListeners($eventName)) {
            foreach ($this->listeners[$eventName] as $priority => $listeners) {
                if (false !== ($key = array_search($listener, $listeners))) {
                    unset($this->listeners[$eventName][$priority][$key]);
                    unset($this->sorted[$eventName]);
                    return;
                }
            }
        }
    }

    protected function sortListeners($eventName)
    {
        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
        }
        //unimos todos los listener que estan en prioridades diferentes.
        $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $method => $params) {
            $params = (array) $params;
            isset($params[1]) || $params[1] = 0; //si no se pasa la prioridad, la creamos.
            //params[0] es el método del objeto a llamar.
            //params[1] es la prioridad.
            $this->addListener($eventName, array($subscriber, $params[0]), $params[1]);
        }
    }

}