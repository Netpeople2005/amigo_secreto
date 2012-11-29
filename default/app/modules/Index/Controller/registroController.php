<?php

namespace Index\Controller;

use KumbiaPHP\Form\Form;
use Index\Model\Usuarios;
use Index\Form\ClavesForm;
use Index\Model\EquiposRegistrados;
use KumbiaPHP\Kernel\Controller\Controller;

class registroController extends Controller
{

    public function index_action()
    {
        if (!EquiposRegistrados::existe($this->getRequest())) {

            $form = new Form('registro');
            $form->add('correo', 'email')->setLabel('Correo Electronico')->required();

            if ($this->getRequest()->isMethod('post')) {
                if ($form->bindRequest($this->getRequest())->isValid()) {
                    return $this->registrar($form['correo']->getValue());
                } else {
                    $this->get('flash')->warning("No se ha podido completar el registro");
                    $this->get('flash')->error($form->getErrors());
                    $this->setResponse('error');
                }
            }

            $this->form = $form;
        } else {
            $this->get('flash')->warning("No se ha podido completar el registro");
            $this->get('flash')->info("Ya tienes un Usuario Asignado");
            $this->setResponse('error');
        }
    }

    protected function registrar($correo)
    {
        if (($this->usuario = Usuarios::aleatorio()) instanceof Usuarios) {

            $this->usuario->begin(); //iniciamos la transaccion

            if ($usr2 = Usuarios::crearRegistro($this->usuario, $this->getRequest())) {
                //acá hacemos el envio del correo.
                if ($this->enviarCorreoRegistro($this->usuario->personaje, $usr2->personaje, $correo)) {
                    //si se envió el correo hacemos el commit.
                    $this->usuario->commit();
                    $this->get('flash')->success("Tu usuario fue creado con exito");
                    $this->get('flash')->info("Se ha enviado un correo a <b>$correo</b> donde 
                            podrás ver el personaje que se te ha Asignado y a quien le regalas...!!!");
                    $this->get('session')->set('correo_registro', $correo);
                    return $this->loguear($this->usuario);
                }
            }
            $this->usuario->rollback();
            $this->get('flash')->error("No se pudo crear el usuario");
        } else {
            $this->get('flash')->warning("No se ha podido completar el registro");
            $this->get('flash')->info("No quedan personajes para asignar");
        }
        $this->setResponse('error');
    }

    public function cambiar_clave_action()
    {
        $this->usuario = Usuarios::findByPK((int) $this->get("security")
                                ->getToken('id'));

        $form = new ClavesForm($this->usuario);

        if ($this->getRequest()->isMethod('post')) {
            if ($form->bindRequest($this->getRequest())->isValid()) {

                $this->usuario->begin();

                if ($this->usuario->save()) {
                    if ($this->enviarCorreoClave($this->usuario)) {
                        //pasamos la clave guardada al usuario de la sesión.
                        $this->get("security")->getToken()
                                        ->getUser()->clave = $this->usuario->clave;
                        $this->get('flash')->success('La contraseña fué actualizada con exito...!!!');
                        $this->usuario->commit();
                        return $this->getRouter()->redirect('index/inicio');
                    }
                }
                $this->usuario->rollback();
                $this->get('flash')->error('No se pudo actualizar la contraseña...!!!');
                $this->get('flash')->error($this->usuario->getErrors());
            } else {
                $this->get('flash')->error($form->getErrors());
            }
        }

        $form['clave_actual'] = $form['nueva_clave'] = $form['nueva_clave2'] = null; //limpiamos los campos
        
        if ($this->get('session')->has('correo_registro')){
             $form['correo'] = $this->get('session')->get('correo_registro');
             $this->get('session')->delete('correo_registro');
        }

        $this->form = $form;
    }

    protected function loguear(Usuarios $usuario)
    {
        return $this->get('firewall')->loginCheck(array(
                    'personaje' => $usuario->personaje,
                ));
    }

    protected function enviarCorreoRegistro($personaje, $aQuienRegala, $correo)
    {
        $mensaje = $this->get('view')->render(array(
            'view' => 'registro',
            'response' => 'email',
            'params' => compact('personaje', 'aQuienRegala', 'correo'),
                ));

        $mail = $this->get('k2_mailer')
                ->setSubject("Registro en Super Amigo Secreto")
                ->setBody($mensaje)
                ->addRecipient($correo);

        try {
            $mail->send();
            return true;
        } catch (\K2\Mail\Exception\MailException $e) {
            $this->get('flash')->error("No se Pudo enviar el correo");
            $this->get('flash')->error($e->getMessage());
            return false;
        }
    }

    protected function enviarCorreoClave(Usuarios $usr)
    {
        $mensaje = $this->get('view')->render(array(
            'view' => 'clave',
            'response' => 'email',
            'params' => array(
                'usuario' => $usr
            ),
                ));

        $mail = $this->get('k2_mailer')
                ->setSubject("Actualizacion de Contraseña")
                ->setBody($mensaje)
                ->addRecipient($usr->correo);

        try {
            $mail->send();
            return true;
        } catch (\K2\Mail\Exception\MailException $e) {
            $this->get('flash')->error("No se Pudo enviar el correo");
            $this->get('flash')->error($e->getMessage());
            return false;
        }
    }

}