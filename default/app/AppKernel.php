<?php
require_once '../../vendor/autoload.php';

use KumbiaPHP\Kernel\Kernel;

/**
 * Description of AppKernel
 *
 * @author manuel
 */
class AppKernel extends Kernel
{

    protected function registerModules()
    {
        $modules = array(
            'KumbiaPHP'   => __DIR__ . '/../../vendor/kumbiaphp/core/src/',
            'Index'       => __DIR__ . '/modules/',
        );

        if (!$this->production) {
            $modules['Demos/Rest']              = __DIR__ . '/modules/';
            $modules['Demos/Router']            = __DIR__ . '/modules/';
            $modules['Demos/Vistas']            = __DIR__ . '/modules/';
            $modules['Demos/Modelos']           = __DIR__ . '/modules/';
            $modules['Demos/SubiendoArchivos']  = __DIR__ . '/modules/';
            $modules['Demos/Seguridad']         = __DIR__ . '/modules/';
        }

        return $modules;
    }

    protected function registerRoutes()
    {
        return array(
            '/'                 => 'Index',
            '/demo/rest'        => 'Demos/Rest',
            '/demo/router'      => 'Demos/Router',
            '/demo/vistas'      => 'Demos/Vistas',
            '/demo/modelos'     => 'Demos/Modelos',
            '/demo/upload'      => 'Demos/SubiendoArchivos',
            '/admin'            => 'Demos/Seguridad',
        );
    }

}