<?php

namespace KumbiaPHP\ActiveRecord;

use \PDOStatement as Base;
use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\ActiveRecord\Event\Events;
use KumbiaPHP\ActiveRecord\Event\BeforeQueryEvent;
use KumbiaPHP\ActiveRecord\Event\AfterQueryEvent;
use KumbiaPHP\EventDispatcher\EventDispatcherInterface;

/**
 * Description of PDOStatement
 *
 * @author manuel
 */
class PDOStatement extends Base
{

    /**
     * 
     * @var EventDispatcherInterface 
     */
    protected static $dispatcher;
    protected $result = NULL;

    public function execute($input_parameters = null)
    {
        //creamos el evento before_query
        $event = new BeforeQueryEvent($this->queryString, (array) $input_parameters);

        //despachamos los eventos que están escuchando
        self::$dispatcher->dispatch(Events::BEFORE_QUERY, $event);

        $this->result = parent::execute($event->getParameters());

        //creamos el evento after_query
        $event = new AfterQueryEvent($this, $event);

        //despachamos los eventos que están escuchando
        self::$dispatcher->dispatch(Events::AFTER_QUERY, $event);

        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public static function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        self::$dispatcher = $dispatcher;
    }

}

PDOStatement::setDispatcher(Kernel::get('dispatcher'));