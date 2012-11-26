<?php

namespace KumbiaPHP\Loader;

/**
 * Clase para el manejo del autoload del fw.
 * 
 * Usa el estandar PSR-0
 * 
 */
final class Autoload
{

    /**
     * Arreglo con las rutas donde se van a buscar las clases.
     * @var array
     */
    private static $directories = array();

    /**
     * registra rutas donde se buscarán clases.
     */
    public static function registerDirectories(array $directories = array())
    {
        self::$directories = array_merge(self::$directories, $directories);
    }

    /**
     * registra la clase en el autoload spl
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Desregistra la clase del autoload spl 
     */
    public static function unregister()
    {
        spl_autoload_unregister(array(__CLASS__, 'autoload'));
    }

    /**
     * Metodo que realiza la busqueda de la clase.
     */
    public static function autoload($className)
    {

        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        foreach (self::$directories as $folder) {
            if (is_file($file = rtrim($folder, '/') . DIRECTORY_SEPARATOR . $fileName)) {
                return include $file;
            }
            //var_dump($file,$fileName);
        }
    }

}
