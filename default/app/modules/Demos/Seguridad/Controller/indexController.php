<?php

namespace Demos\Seguridad\Controller;

use KumbiaPHP\Kernel\Controller\Controller;
use KumbiaPHP\Kernel\Response;

/**
 * Ejemplo de un controlador REST FULL
 * 
 * Este controlador puede manejar peticiones de tipo rest
 *
 * @author maguirre
 */
class indexController extends Controller
{

    public function index_action()
    {
        $this->usuario = $this->get('security')->getToken()->getUser();
    }

    public function login_action()
    {
        $this->form = new \KumbiaPHP\Form\Form('form_login');

        $this->form->setAction('_autenticate')
                ->add('username')->setLabel('Nombre de Usuario: ');

        $this->form->add('password', 'password')->setLabel('Contraseña: ');

        if ($this->get('flash')->has('LOGIN_ERROR')) {
            $this->form->setErrors($this->get('flash')->get('LOGIN_ERROR'));
        }
    }

}
