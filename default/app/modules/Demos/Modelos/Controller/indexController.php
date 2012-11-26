<?php

namespace Demos\Modelos\Controller;

use Demos\Modelos\Model\Usuarios;
use Scaffold\Controller\ScaffoldController;

class indexController extends ScaffoldController
{
    
    protected function beforeFilter()
    {
        //$this->setTemplate(NULL);
        $this->model = new Usuarios();
    }
}