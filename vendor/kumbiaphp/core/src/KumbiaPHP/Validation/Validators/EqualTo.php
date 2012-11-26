<?php

namespace KumbiaPHP\Validation\Validators;

use KumbiaPHP\Validation\Validators\ValidatorInterface;
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
 * Realiza validacion para campo con valor no nulo
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Validators
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class EqualTo extends ValidatorBase
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
        $value1 = self::getValue($object, $column);
        $value2 = self::getValue($object, $params['field']);
        if ($value1 !== $value2) {
            if (!isset($params['message'])) {
                $params['message'] =  "El campo $column debe ser igual al campo {$params['field']}";
            }
            if ( $object instanceof \KumbiaPHP\Form\Form){
                $params['field'] = $object[$params['field']]['label'];
            }
            self::createErrorMessage($object, $column, $params);
            return FALSE;
        }

        return TRUE;
    }

}
