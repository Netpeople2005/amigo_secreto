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
 * Realiza validacion para campo con valor por defecto
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Validators
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class DefaultField extends ValidatorBase
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
        // Se ha indicado el campo y no se considera nulo, por lo tanto no se tomara por defecto
        if (isset($object->$column) && $object->$column != '') {
            // Se considera con valor por defecto cuando sea nulo
            return FALSE;
        }

        // Valor por defecto
        return TRUE;
    }

}
