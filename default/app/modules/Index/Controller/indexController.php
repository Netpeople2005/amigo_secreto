<?php

namespace Index\Controller;

use Index\Model\Usuarios;
use Index\Model\EquiposRegistrados;
use KumbiaPHP\Kernel\Controller\Controller;

class indexController extends Controller
{

    public function index_action()
    {
        return $this->getRouter()->forward('regalos/');
    }

    public function inicio_action()
    {
        if ( null === $this->get('security')->getToken('clave') ){
            $this->get('flash')->warning("Debes Crear Tu Contraseña...!!!");
            return $this->getRouter()->forward('registro/cambiar_clave');
        }
        
        return $this->getRouter()->forward('regalos/');
    }
}