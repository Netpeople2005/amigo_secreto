<?php

namespace Index\Services;

use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Validation\ValidationBuilder;
use KumbiaPHP\Security\Event\SecurityEvent;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 * Description of Servicio
 *
 * @author manuel
 */
class Servicio
{

    /**
     *
     * @var ContainerInterface 
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setSession(\KumbiaPHP\Kernel\Session\SessionInterface $sesion)
    {
        
    }

    public function onRequest(RequestEvent $event)
    {
        
    }

    public function onLogin(SecurityEvent $event)
    {
        $this->container->get('flash')->success("Bienvenido al sistema <b>{$event->getSecutiry()->getToken()->getUsername()}</b>");
    }

    public function cerrandoSesion(SecurityEvent $event)
    {
        $horas = date('H:i:s');
        $fecha = date('d-m-Y');
        $this->container->get('flash')->success("Sesión cerrada a las <b>{$horas}</b> Horas del Día <b>{$fecha}</b>");
    }

}