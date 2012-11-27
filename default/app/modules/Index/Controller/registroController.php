<?php

namespace Index\Controller;

use Index\Model\Usuarios;
use Index\Model\EquiposRegistrados;
use KumbiaPHP\Kernel\Controller\Controller;

class registroController extends Controller
{

    public function index_action()
    {
        $this->get('flash')->info("IP {$this->getRequest()->getClientIp()}");
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
        
    }

    protected function loguear(Usuarios $usuario)
    {
        return $this->get('firewall')->loginCheck(array(
                    'personaje' => $usuario->personaje,
                    'clave' => $usuario->clave,
                ));
    }

}