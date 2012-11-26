<?php

namespace KumbiaPHP\Form\Field;

/**
 * Description of ChoiceInterface
 *
 * @author Administrador
 */
interface ChoiceInterface {

    /**
     * Establece las opciones a usar en el campo.
     * 
     * @param array $options
     * @return ChoiceField 
     */
    public function setOptions(array $options);

    /**
     * Devuelve las opciones establecidas para el campo.
     * @return type 
     */
    public function getOptions();

    /**
     * Establece las opciones a partir del resultado de una consulta con 
     * el Active Record
     *
     * @param array $options resultado de una consulta ó un array.
     * @param string $indexName Nombre del campo|columna que será el valor de las opciones
     * @param string $columName Nombre del campo|Columna que será el label de las opciones
     * @return ChoiceField 
     */
    public function setOptionsFromResultset($options, $indexName, $columName);

    /**
     * Valida que el valor seleccionado exista dentro de las opciones del campo.
     * @param string $message mensaje de error en caso de fallar la validación
     * @return ChoiceField 
     */
    public function inListValidation($message = 'El valor del campo %s no está en la lista de opciones');
}
