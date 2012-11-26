<?php

namespace KumbiaPHP\ActiveRecord\Config;

use KumbiaPHP\Kernel\Kernel;
use ActiveRecord\Config\Config;
use ActiveRecord\Config\Parameters;

/**
 * Description of Reader
 *
 * @author maguirre
 */
class Reader
{

    public static function readDatabases()
    {
        /* @var $app \KumbiaPHP\Kernel\AppContext */
        $app = Kernel::get('app.context');
        $ini = $app->getAppPath() . 'config/databases.ini';
        foreach (parse_ini_file($ini, TRUE) as $configName => $params) {
            Config::add($parameter = new Parameters($configName, $params));
            if ('sqlite' === $parameter->getType()) {
                $dbName = Kernel::getParam('app_dir') . ltrim($parameter->getDbName(), '/');
                $parameter->setDbName(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $dbName));
            }
        }
        if (Kernel::getParam('config.database')) {
            //lo seteamos solo si se ha definido.
            $database = Kernel::getParam('config.database');
            if (!Config::has($database)) {
                throw new \LogicException("El valor database=$database del config.ini no concuerda con ninguna sección del databases.ini");
            }
            Config::setDefaultId($database);
        }
    }

}
