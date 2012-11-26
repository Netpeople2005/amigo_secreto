<?php

namespace KumbiaPHP\Form\Field;

use KumbiaPHP\Form\Field\Text;

class Color extends Text
{

    public function __construct($fieldName)
    {
        parent::__construct($fieldName);
        $this->setType('color');
    }

}