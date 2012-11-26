<?php

namespace KumbiaPHP\ActiveRecord\Event;

use KumbiaPHP\ActiveRecord\PDOStatement;
use KumbiaPHP\ActiveRecord\Event\BeforeQueryEvent;

/**
 * Description of BeforeEvent
 *
 * @author maguirre
 */
class AfterQueryEvent extends BeforeQueryEvent
{

    /**
     *
     * @var PDOStatement 
     */
    protected $statement;

    function __construct(PDOStatement $statement, BeforeQueryEvent $event)
    {
        $this->statement = $statement;
        $this->setQuery($event->getQuery());
        $this->setParameters($event->getParameters());
        //que no los construya, eso debe hacerlo el programador si lo necesita.
        $this->queryType = $event->getQueryType(false);
    }

    /**
     * @return PDOStatement 
     */
    public function getStatement()
    {
        return $this->statement;
    }

    public function getResult()
    {
        return $this->statement->getResult();
    }

}
