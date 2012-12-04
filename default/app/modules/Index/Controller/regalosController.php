<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of regalosController
 *
 * @author apatino
 * @colaborator ohernandez
 */

namespace Index\Controller;

use Index\Model\Usuarios;
use KumbiaPHP\Kernel\Controller\Controller;
use KumbiaPHP\Form\Form;
use Index\Model\EquiposRegistrados;

class regalosController extends Controller{
    
    
    public function index_action() {
            $this->activeUser = \KumbiaPHP\View\View::get('security')->getToken()->getUser();
        die('mame');
        if (!EquiposRegistrados::existe($this->getRequest())) {
            $this->activeUser = \KumbiaPHP\View\View::get('security')->getToken()->getUser();
        }else{
            $this->activeUser = "no tiene usuario registrado";
//            KumbiaPHP\View\View::get('security')->getToken('personaje');
//            KumbiaPHP\View\View::get('security')->getToken('imagen');
        }
    }

    public function ver_regalos_action(){
        $this->usuarios = Usuarios::findAll();
    }
    
}
