<?php

namespace Index\Form;

use KumbiaPHP\Form\Form;

class ClavesForm extends Form
{

    protected function init()
    {
        $this->add('correo', 'email')
                ->setLabel('Correo Electronico')
                ->required();
        
        $this->add('nueva_clave', 'password')
                ->setLabel('Contraseña')
                ->required();

        $this->add('nueva_clave2', 'password')
                ->setLabel('Repetir Contraseña')
                ->required()
                ->equalTo('nueva_clave', 'Las contraseñas no coinciden...!!!');

        if ($this->model instanceof \KumbiaPHP\ActiveRecord\ActiveRecord &&
                null != $this->model->clave) {
            //si la clave no está vacia, quiere decir que ya posee una clave.
            //por lo que se le debe pedir la clave actual
            $this->add('clave_actual', 'password')
                    ->setLabel('Contraseña Actual')
                    ->required();
            
            //cambiamos ademas los label de clave y clave2
            $this->getField('nueva_clave')->setLabel('Nueva Contraseña');
            $this->getField('nueva_clave2')->setLabel('Repetir Nueva Contraseña');
        }
    }

}

