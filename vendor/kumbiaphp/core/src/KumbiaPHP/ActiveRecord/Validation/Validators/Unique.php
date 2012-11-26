<?php

namespace KumbiaPHP\ActiveRecord\Validation\Validators;

use KumbiaPHP\Validation\Validatable;
use KumbiaPHP\Validation\Validators\ValidatorBase;

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
 * Realiza validacion para campo con valor unico
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Validators
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class Unique extends ValidatorBase
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
        if ($object instanceof \KumbiaPHP\Form\Form) {
            $object = $object->getData();
        }
        if (!$object instanceof \KumbiaPHP\ActiveRecord\ActiveRecord) {
            throw new \LogicException(sprintf("El metodo \"validate\" de la clase \"%s\" espera un objeto ActiveRecord", __CLASS__));
        }
        // Condiciones
        $q = $object->createQuery();

        $values = array();

        // Si es para actualizar debe verificar que no sea la fila que corresponde
        // a la clave primaria
        if ($update) {
            // Obtiene la clave primaria
            $pk = $object->metadata()->getPK();

            if (is_array($pk)) {
                // Itera en cada columna de la clave primaria
                $conditions = array();
                foreach ($pk as $k) {
                    // Verifica que este definida la clave primaria
                    if (!isset($object->$k) || $object->$k === '') {
                        throw new \LogicException("Debe definir valor para la columna $k de la clave primaria");
                    }

                    $conditions[] = "$k = :pk_$k";
                    $q->bindValue("pk_$k", $object->$k);
                }

                $q->where('NOT (' . implode(' AND ', $conditions) . ')');
            } else {
                // Verifica que este definida la clave primaria
                if (!isset($object->$pk) || $object->$pk === '') {
                    throw new \LogicException("Debe definir valor para la clave primaria $pk");
                }

                $q->where("NOT $pk = :pk_$pk");
                $q->bindValue("pk_$pk", $object->$pk);
            }
        }
        $values[$column] = $object->$column;

        $q->where("$column = :$column")->bind($values);

        // Verifica si existe
        if ($object->existsOne()) {
            self::createErrorMessage($object, $column, $params);
            return FALSE;
        }

        return TRUE;
    }

}
