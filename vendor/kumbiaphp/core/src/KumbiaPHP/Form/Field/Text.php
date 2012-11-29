<?php

namespace KumbiaPHP\Form\Field;

use KumbiaPHP\Form\Field\AbstractField;

/**
 * Description of FormFieldText
 *
 * @author manuel
 */
class Text extends AbstractField
{

    public function __construct($fieldName)
    {
        parent::__construct($fieldName);
        $this->setType('text');
    }

    public function render()
    {
        return '<input ' . $this->attrsToString() . ' />' . PHP_EOL;
    }

    public function maxLength($max, $min = 0, $message = 'El campo {label} debe tener mínimo {min} caracteres y maximo {max}')
    {
        $this->validationBuilder->lengthBetween($this->getFieldName(), array(
            'message' => $message,
            'max' => $max,
            'min' => $min,
                ), false);
        return $this->attrs(array('maxlength' => $max));
    }

    public function equalTo($field, $message = 'El campo {label} debe ser igual al campo {field}')
    {
        $this->validationBuilder->equalTo($this->getFieldName(), array(
            'message' => $message,
            'field' => $field,
                ), false);
        return $this;
    }

}

