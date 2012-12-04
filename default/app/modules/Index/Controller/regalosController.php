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

class regalosController extends Controller
{


    public function index_action()
    {
        $this->usuarios = Usuarios::findAll();
    }

    public function editar_regalos_action()
    {
        $user = Usuarios::findByPK($this->get('security')->getToken('id'));
        $form = new Form($user);
        $form->add('regalo_esperado', 'textarea')->setLabel('Ingrese el Regalo que desea recibir:')->required();
        $this->form = $form;

        if ($this->getRequest()->isMethod('post')) {
            if($form->bindRequest($this->getRequest())->isValid()){
                if ($user->save()) {
                    $this->get('flash')->success("Regalo deseado guardado!!!");
                } else {
                    $this->get('flash')->error($user->getErrors());
                }
            }
        }
    }
}
