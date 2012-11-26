<?php

namespace KumbiaPHP\Form\Field;

use KumbiaPHP\Form\Field\Text;

/**
 * Description of FormFieldText
 *
 * @author manuel
 */
class Email extends Text
{

    public function __construct($fieldName)
    {
        parent::__construct($fieldName);
        $this->setType('email');
    }

}