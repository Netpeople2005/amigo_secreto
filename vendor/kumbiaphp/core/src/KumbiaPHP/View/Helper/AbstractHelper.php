<?php

namespace KumbiaPHP\View\Helper;

use KumbiaPHP\Kernel\AppContext;

/**
 * Description of AbstractHelper
 *
 * @author manuel
 */
abstract class AbstractHelper
{

    /**
     *
     * @var AppContext 
     */
    protected static $app;

    /**
     * 
     * @param AppContext $app 
     */
    public static function setAppContext(AppContext $app)
    {
        self::$app = $app;
    }

    /**
     * Convierte los argumentos de un metodo de parametros por nombre a un string con los atributos
     *
     * @param array $params argumentos a convertir
     * @return string
     */
    public static function getAttrs($params)
    {
        $data = '';
        foreach ($params as $k => $v) {
            $data .= " $k=\"$v\"";
        }
        return $data;
    }

}