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
 * Realiza validacion para campo con longitud de caracteres
 * comprendida en un rango de valores
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Validators
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class LengthBetweenValidator implements ValidatorInterface
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
	public static function validate($object, $column, $params = NULL, $update = FALSE)
	{
		if(!Validate::between($object->$column, $params['min'], $params['max'])) {
			if(isset($params['message'])) {
				Flash::error($params['message']);
			} else {
				Flash::error("El campo $column debe tener una cantidad de caracteres comprendida entre $min y $max");
			}
			
			return FALSE;
		}
				
		return TRUE;
	}
}
