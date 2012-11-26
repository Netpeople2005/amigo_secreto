<?php

namespace KumbiaPHP\Security\Config;

use KumbiaPHP\Kernel\AppContext;

/**
 * Description of Reader
 *
 * @author manuel
 */
abstract class Reader
{

    protected static $config;

    public static function readSecurityConfig(AppContext $app)
    {
        self::$config = parse_ini_file($app->getAppPath() . '/config/security.ini', true);
    }

    public static function get($name = NULL)
    {
        $namespaces = explode('.', $name);
        switch (count($namespaces)) {
            case 3:
                if (isset(self::$config[$namespaces[0]][$namespaces[1]][$namespaces[2]])) {
                    return self::$config[$namespaces[0]][$namespaces[1]][$namespaces[2]];
                }
                break;
            case 2:
                if (isset(self::$config[$namespaces[0]][$namespaces[1]])) {
                    return self::$config[$namespaces[0]][$namespaces[1]];
                }
                break;
            case 1:
                if (isset(self::$config[$namespaces[0]])) {
                    return self::$config[$namespaces[0]];
                }
                break;
        }
        return NULL;
    }

}