<?php

namespace Demos\Vistas\Controller;

use KumbiaPHP\Kernel\Controller\Controller;

/**
 * Ejemplo de uso de las vistas en el nuevo FW
 *
 * @author manuel
 */
class indexController extends Controller
{

    public function index_action()
    {
        /* 
         * Aqui le decimos al fw que queremos usar el template de nuestro
         * módulo llamado "mi_template.phtml"
         */
        $this->setTemplate('Demos/Vistas:mi_template');
    }

    public function saludo_action($nombre)
    {
        $this->tuNombre = $nombre;
        
        /*
         * Escogemos la vista otra_accion.phtml
         */
        $this->setView('otra_accion');
        
        /* 
         * Aqui le decimos al fw que queremos usar el template de nuestro
         * módulo llamado "mi_template.phtml"
         */
        $this->setTemplate('Demos/Vistas:mi_template');
    }

}