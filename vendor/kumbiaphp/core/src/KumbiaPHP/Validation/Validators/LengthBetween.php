<?php

namespace KumbiaPHP\Validation\Validators;

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
 * Realiza validacion para campo con longitud de caracteres
 * comprendida en un rango de valores
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Validators
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class LengthBetween extends ValidatorBase
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
        $value = self::getValue($object, $column);
        if (strlen($value) < $params['min'] || strlen($value) > $params['max']) {
            if (!isset($params['message'])) {
                $params['message'] = "El campo $column debe estár entre {$params['min']} y {$params['max']}";
            }
            self::createErrorMessage($object, $column, $params);
            return FALSE;
        }

        return TRUE;
    }

}
