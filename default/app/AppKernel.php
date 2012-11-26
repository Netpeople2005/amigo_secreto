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
            'KumbiaPHP' => __DIR__ . '/../../vendor/kumbiaphp/core/src/',
            'Index' => __DIR__ . '/modules/',
        );

        if (!$this->isProduction()) {
            $modules['K2/Debug'] = __DIR__ . '/../../vendor/';
        }

        return $modules;
    }

    protected function registerRoutes()
    {
        return array(
            '/' => 'Index',
        );
    }

}