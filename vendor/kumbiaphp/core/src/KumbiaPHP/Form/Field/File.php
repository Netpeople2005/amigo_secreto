<?php

namespace KumbiaPHP\Form\Field;

use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\Form\Field\Text;

/**
 * Description of FormFieldText
 *
 * @author manuel
 */
class File extends Text
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
        return Kernel::get('request')->files
            ->get($this->getFieldName(), $this->formName);
    }

}