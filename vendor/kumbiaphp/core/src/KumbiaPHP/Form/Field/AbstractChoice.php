<?php

namespace KumbiaPHP\Form\Field;

use KumbiaPHP\Form\Field\AbstractField;
use KumbiaPHP\Form\Field\ChoiceInterface;

/**
 * Description of FormFieldText
 *
 * @author manuel
 */
abstract class AbstractChoice extends AbstractField implements ChoiceInterface
{

    protected $separator = '&nbsp;&nbsp;&nbsp;';

    /**
     * Opciones para el campo.
     * 
     * @var array 
     */
    protected $options = array();

    public function __construct($fieldName)
    {
        parent::__construct($fieldName);
    }

    /**
     * Establece las opciones a usar en el campo.
     * 
     * @param array $options
     * @return ChoiceField 
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Devuelve las opciones establecidas para el campo.
     * @return type 
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function setOptionsFromResultset($options, $indexName, $columName)
    {
        $data = array();
        foreach ($options as $opt) {
            if (is_array($opt)) {
                $data[$opt[$indexName]] = $opt[$columName];
            } else {
                $data[$opt->$indexName] = $opt->$columName;
            }
        }
        return $this->setOptions($data);
    }

    public function getSeparator()
    {
        return $this->separator;
    }

    public function setSeparator($separator)
    {
        $this->separator = $separator;
        return $this;
    }

    public function inListValidation($message = 'El valor del campo {label} no está en la lista de opciones')
    {
        $this->validationBuilder->inList($this->getFieldName(), array(
            'message' => $message,
            'list' => $this->getOptions()
        ));
        return $this;
    }

    public function required($required = TRUE, $message = 'Debe seleccionar al menos una opción para el campo {label}')
    {
        parent::required($required, $message);
        unset($this->_attrs['required']);
        return $this;
    }

    public function setValue($value)
    {
        if (is_array($value) && count($value) &&
                (current($value) instanceof \KumbiaPHP\ActiveRecord\ActiveRecord)) {
            $values = array();
            foreach ($value as $item) {
                if (isset($item->{$item->metadata()->getPK()})) {
                    $values[] = $item->{$item->metadata()->getPK()};
                }
            }
            return parent::setValue($values);
        } else {
            return parent::setValue($value);
        }
    }

}