<?php

namespace KumbiaPHP\ActiveRecord\Validation\Validators;

use KumbiaPHP\Validation\Validators\ValidatorBase;
use KumbiaPHP\Validation\Validatable;

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
 * Realiza validacion para campo clave primaria
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Validators
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class Primary extends ValidatorBase
{

    /**
     * Metodo para validar
     *
     * @param ActiveRecord $object objeto ActiveRecord
     * @param string $column nombre de columna a validar
     * @param array $params parametros de configuracion
     * @param boolean $update indica si es operacion de actualizacion
     * @return boolean
     */
    public static function validate(Validatable $object, $column, $params = NULL, $update = FALSE)
    {
        if (!$object instanceof \KumbiaPHP\ActiveRecord\ActiveRecord) {
            throw new \LogicException(sprintf("El metodo \"validate\" de la clase \"%s\" espera un objeto ActiveRecord", __CLASS__));
        }
        // Condiciones
        $q = $object->get();

        if (is_array($column)) {
            $values = array();

            // Establece condiciones
            foreach ($column as $k) {
                // En un indice UNIQUE si uno de los campos es NULL, entonces el indice
                // no esta completo y no se considera la restriccion
                if (!isset($object->$k) || $object->$k === '') {
                    return TRUE;
                }

                $values[$k] = $object->$k;
                $q->where("$k = :$k");
            }

            // Si es para actualizar debe verificar que no sea la fila que corresponde
            // a la clave primaria
            if ($update) {
                $conditions = array();
                foreach ($column as $k) {
                    $conditions[] = "$k = :pk_$k";
                    $q->bindValue("pk_$k", $object->$k);
                }

                $q->where('NOT (' . implode(' AND ', $conditions) . ')');
            }

            $q->bind($values);

            // Verifica si existe
            if ($object->existsOne()) {
                return FALSE;
            }
        } else {
            // Si es para actualizar debe verificar que no sea la fila que corresponde
            // a la clave primaria
            if ($update) {
                $q->where("NOT $column = :pk_$column");
                $q->bindValue("pk_$column", $object->$column);
            }

            $q->where("$column = :$column")->bindValue($column, $object->$column);

            // Verifica si existe
            if ($object->existsOne()) {
                return FALSE;
            }
        }

        return TRUE;
    }

}
