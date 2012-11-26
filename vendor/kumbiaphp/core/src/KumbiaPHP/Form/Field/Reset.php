<?php

namespace KumbiaPHP\Form\Field;

use KumbiaPHP\Form\Field\AbstractField;

/**
 * Description of FormFieldText
 *
 * @author manuel
 */
class Reset extends AbstractField
{

    public function __construct($fieldName = NULL)
    {
        parent::__construct($fieldName);
        $this->setType('reset');
    }

    public function render()
    {
        return '<input ' . $this->attrsToString() . ' />' . PHP_EOL;
    }

}