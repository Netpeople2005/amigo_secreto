<?php

namespace KumbiaPHP\Form\Field;

use KumbiaPHP\Form\Field\Field;

/**
 * Description of FormFieldText
 *
 * @author manuel
 */
class File extends Field
{

    public function __construct($fieldName)
    {
        parent::__construct($fieldName);
        $this->setType('file');
    }

    /**
     * Reescritura del metodo para evitar el seteo del valor del form.
     * @param type $value 
     */
    public function setValue($value)
    {
        //no permitirmos el seteo de vvalores para los campos field
    }

    public function getValue()
    {
        if (array_key_exists($this->getFieldName(), $_FILES)) {
            if ($_FILES[$this->getFieldName()]['size'] > 0) {
                return $_FILES[$this->getFieldName()];
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }

}