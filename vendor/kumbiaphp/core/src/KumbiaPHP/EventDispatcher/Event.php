<?php

namespace KumbiaPHP\EventDispatcher;

/**
 * Clase base que se pasará a los escuchas de eventos del fw.
 * 
 * Esta clase solo tiene dos métodos, uno que puede detener la llamada a escuchas
 * posteriores y otro que verifica si se ha mandado a detener la ejecucion
 * de los siguientes escuchas.
 *
 * @author manuel
 */
class Event
{

    /**
     * Indica si se detuvo la propagacion de la ejecucion de los escuchas.
     *
     * @var boolean 
     */
    protected $propagationStopped = FALSE;

    /**
     * Detiene la ejecucion de un evento para escuchas posteriores. 
     */
    public function stopPropagation()
    {
        $this->propagationStopped = TRUE;
    }

    /**
     * Verifica si algun escucha ha detenido la ejecucion del evento.
     *
     * @return boolean 
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

}