<?php

namespace KumbiaPHP\Validation\Validators;

use KumbiaPHP\Validation\Validatable;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Interface para validador de ActiveRecord
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Validators
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
abstract class ValidatorBase
{

    protected static $lastError;

    /**
     *
     * @var ContainerInterface 
     */
    protected static $container;

    /**
     * Metodo para validar
     *
     * @param ActiveRecord $object objeto ActiveRecord
     * @param string $column nombre de columna a validar
     * @param array $params parametros de configuracion
     * @param boolean $update indica si es operacion de actualizacion
     * @return boolean
     */
    public static function validate(Validatable $object, $column, $params = null, $update = false)
    {
        return true;
    }

    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    public static function getLastError()
    {
        return self::$lastError;
    }

    protected static function getValue(Validatable $object, $column)
    {
        if ($object instanceof \KumbiaPHP\Form\Form) {//para trabajar la Lib Form de K2
            if (isset($object[$column])) {
                return $object[$column]->getValue();
            } elseif (!is_object($object = $object->getData())) {
                return null;
            }
        }
        $reflection = new \ReflectionObject($object);
        if ($reflection->hasProperty($column)) {
            $attribute = $reflection->getProperty($column);
            $attribute->setAccessible(true);
            return $attribute->getValue($object);
        } else {
            return null;
        }
    }

    protected static function createErrorMessage(Validatable $object, $column, $params)
    {
        if (isset($params['message'])) {

            if (preg_match_all("/{(?'item'.+?)}/", $params['message'], $matches)) {
                if (self::$container->has('translator')) {//solo si existe el servicio en el contenedor.
                    $params['message'] = self::$container->get('translator')->trans($params['message']);
                }
                foreach ($matches['item'] as $item) {
                    if ('label' === $item && $object instanceof \KumbiaPHP\Form\Form) {//para trabajar la Lib Form de K2
                        $value = $object[$column]['label'];
                    } else {
                        if (!isset($params[$item])) {
                            continue;
                        }
                        $value = $params[$item];
                    }
                    $params['message'] = str_replace('{' . $item . '}', $value, $params['message']);
                }
            }
        } else {
            $params['message'] = null;
        }

        self::$lastError = $params['message'];
    }

}

