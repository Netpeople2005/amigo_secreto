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
 * Realiza validacion para campo con valor unico
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @subpackage Validators
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class UniqueValidator implements ValidatorInterface
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
		// Condiciones
		$q = $object->createQuery();
		
		$values = array();
		
		// Si es para actualizar debe verificar que no sea la fila que corresponde
		// a la clave primaria
		if($update) {	
			// Obtiene la clave primaria
			$pk = $object->metadata()->getPK();
			
			if(is_array($pk)) {
				// Itera en cada columna de la clave primaria
				$conditions = array();
				foreach($pk as $k) {
					// Verifica que este definida la clave primaria
					if(!isset($object->$k) || $object->$k === '') {
						throw new KumbiaException("Debe definir valor para la columna $k de la clave primaria");
					}
					
					$conditions[] = "$k = :pk_$k";
					$q->bindValue("pk_$k", $object->$k);
				}
				
				$q->where('NOT (' . implode(' AND ', $conditions) . ')');
			} else {
				// Verifica que este definida la clave primaria
				if(!isset($object->$pk) || $object->$pk === '') {
					throw new KumbiaException("Debe definir valor para la clave primaria $pk");
				}
						
				$q->where("NOT $pk = :pk_$pk");
				$q->bindValue("pk_$pk", $object->$pk);
			}
		}
		
		if(is_array($column)) {	
			// Establece condiciones con with
			foreach($column as $k) {
				// En un indice UNIQUE si uno de los campos es NULL, entonces el indice
				// no esta completo y no se considera la restriccion
				if(!isset($object->$k) || $object->$k === '') {
					return TRUE;
				}
				
				$values[$k] = $object->$k;
				$q->where("$k = :$k");
			}
			
			$q->bind($values);
				
			// Verifica si existe
			if($object->existsOne()) {
				if(!isset($params['message'])) {
					$v = implode("', '", array_values($values));
					$c = implode("', '", array_keys($values));
					$msg = "Los valores '$v' ya existen para los campos '$c'";
				} else {
					$msg = $params['message'];
				}
					
				Flash::error($msg);
				return FALSE;
			}
		} else {		
			$values[$column] = $object->$column;
			
			$q->where("$column = :$column")->bind($values);
			// Verifica si existe
			if($object->existsOne()) {
				if(!isset($params['message'])) {
					$msg = "El valor '{$object->$column}' ya existe para el campo $column";
				} else {
					$msg = $params['message'];
				}
				
				Flash::error($msg);
				return FALSE;
			}
		}
		
		return TRUE;
	}
}
