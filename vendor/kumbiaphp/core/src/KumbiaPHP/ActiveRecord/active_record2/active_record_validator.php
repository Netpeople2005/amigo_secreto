<?php

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
 * Validador para ActiveRecord
 * 
 * @category   Kumbia
 * @package    ActiveRecord
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class ActiveRecordValidator
{

    /**
     * Validar en caso de crear
     * 
     * @param ActiveRecord $object
     * @return boolean
     */
    public final static function validateOnCreate($object)
    {
        return self::_validate($object);
    }

    /**
     * Validar en caso de actualizar
     * 
     * @param ActiveRecord $object
     * @return boolean
     */
    public static function validateOnUpdate($object)
    {
        return self::_validate($object, TRUE);
    }

    /**
     * Efectua las validaciones
     * 
     * @param ActiveRecord $object
     * @param boolean $update
     * @return boolean
     */
    private static function _validate($object, $update = FALSE)
    {
        // Obtiene los validadores
        $validators = $object->validators();

        // Si no hay validadores definidos
        if (!$validators) {
            return TRUE;
        }

        // Columnas con valor por defectos
        $default = array();

        // Verifica si existe columnas con valor por defectos
        if (isset($validators['default'])) {
            // @see DefaultValidator
            require_once CORE_PATH . "libs/ActiveRecord/active_record2/validators/default_validator.php";

            // Itera en cada definicion de validacion
            foreach ($validators['default'] as $v) {
                // Verifica las condiciones para cuando la columna es con valor por defecto
                $default[$v] = DefaultValidator::validate($object, $v, NULL, $update);
            }

            // Aprovecha y libera memoria :)
            unset($validators['default']);
        }

        // Por defecto es valido
        $valid = TRUE;

        // Verifica si existe columnas no nulas
        if (isset($validators['notNull'])) {
            // @see NotNullValidator
            require_once CORE_PATH . "libs/ActiveRecord/active_record2/validators/not_null_validator.php";

            // Itera en cada definicion de validacion
            foreach ($validators['notNull'] as $v) {
                // Si es una columna sin configuracion especial
                if (is_string($v)) {
                    // Si es con valor por defecto entonces salta la validacion
                    if (isset($default[$v]) && $default[$v]) {
                        continue;
                    }

                    $column = $v;
                    $params = NULL;
                } else {
                    // Si es con valor por defecto entonces salta la validacion
                    if (isset($default[$v[0]]) && $default[$v[0]]) {
                        continue;
                    }

                    $column = $v[0];
                    unset($v[0]);
                    $params = $v;
                }

                // Valida si el campo
                $valid = NotNullValidator::validate($object, $column, $params, $update) && $valid;
            }

            // Aprovecha y libera memoria :)
            unset($validators['notNull']);
        }

        // Realiza el resto de las validaciones a las columnas
        foreach ($validators as $validator => $validations) {
            // Clase validadora
            $class = $validator . 'validator';

            // Carga el validador de ser necesario
            if (!class_exists($class, FALSE)) {
                self::_load($validator);
            }

            // Itera en cada definicion de validacion
            foreach ($validations as $v) {

                // Si es una columna sin configuracion especial
                if (is_string($v)) {
                    // Si es con valor por defecto entonces salta la validacion
                    if (isset($default[$v]) && $default[$v]) {
                        continue;
                    }

                    $column = $v;
                    $params = NULL;
                } else {
                    // Si es con valor por defecto entonces salta la validacion
                    if (is_string($v[0]) && isset($default[$v[0]]) && $default[$v[0]]) {
                        continue;
                    }

                    $column = $v[0];
                    unset($v[0]);
                    $params = $v;
                }

                if (is_array($column) || (isset($object->$column) && $object->$column != '')) {
                    $valid = call_user_func(array($class, 'validate'), $object, $column, $params, $update) && $valid;
                }
            }
        }

        // Resultado de validacion
        return $valid;
    }

    /**
     * Carga un validador
     *
     * @param string $validator
     * @throw KumbiaException
     */
    private static function _load($validator)
    {
        // Convierte a smallcase
        $validatorSmall = Util::smallcase($validator);

        $file = APP_PATH . "extensions/validators/{$validatorSmall}_validator.php";
        if (!is_file($file)) {
            $file = CORE_PATH . "libs/ActiveRecord/active_record2/validators/{$validatorSmall}_validator.php";
            if (!is_file($file)) {
                throw new KumbiaException("Validador $validator no encontrado");
            }
        }

        include $file;
    }

}
