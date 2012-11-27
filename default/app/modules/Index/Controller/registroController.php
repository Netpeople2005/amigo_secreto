<?php

namespace Index\Controller;

use Index\Form\ClavesForm;
use Index\Model\Usuarios;
use Index\Model\EquiposRegistrados;
use KumbiaPHP\Kernel\Controller\Controller;

class registroController extends Controller
{

    public function index_action()
    {
        if (true || !EquiposRegistrados::existe($this->getRequest())) {

            if (($this->usuario = Usuarios::aleatorio()) instanceof Usuarios) {

                if (Usuarios::crearRegistro($this->usuario, $this->getRequest())) {
                    $this->get('flash')->success("Tu usuario fue creado con exito, eres {$this->usuario->personaje}");
                    return $this->loguear($this->usuario);
                } else {
                    $this->get('flash')->error("No se pudo crear el usuario");
                }
            } else {
                $this->get('flash')->warning("No se ha podido completar el registro");
                $this->get('flash')->info("No quedan personajes para asignar");
            }
        } else {
            $this->get('flash')->warning("No se ha podido completar el registro");
            $this->get('flash')->info("Ya tienes un Usuario Asignado");
        }
    }

    public function cambiar_clave_action()
    {
        $this->usuario = Usuarios::findByPK((int) $this->get("security")
                                ->getToken('id'));

        $form = new ClavesForm($this->usuario);

        if ($this->getRequest()->isMethod('post')) {
            if ($form->bindRequest($this->getRequest())->isValid()) {
                if ($this->usuario->save()) {
                    //pasamos la clave guardada al usuario de la sesión.
                    $this->get("security")->getToken()
                                    ->getUser()->clave = $this->usuario->clave;
                    $this->get('flash')->success('La contraseña fué actualizada con exito...!!!');
                    return $this->getRouter()->redirect('index/inicio');
                } else {
                    $this->get('flash')->error('No se pudo actualizar la contraseña...!!!');
                    $this->get('flash')->error($this->usuario->getErrors());
                }
            } else {
                $this->get('flash')->error($form->getErrors());
            }
        }

        $form['clave_actual'] = $form['nueva_clave'] = $form['nueva_clave2'] = null; //limpiamos los campos

        $this->form = $form;
    }

    protected function loguear(Usuarios $usuario)
    {
        return $this->get('firewall')->loginCheck(array(
                    'personaje' => $usuario->personaje,
                ));
    }

}