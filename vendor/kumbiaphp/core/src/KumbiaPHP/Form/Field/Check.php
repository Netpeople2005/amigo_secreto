<?php

namespace KumbiaPHP\Form\Field;

use KumbiaPHP\Form\Field\AbstractChoice;

/**
 * Description of FormFieldText
 *
 * @author manuel
 */
class Check extends AbstractChoice
{

    public function __construct($fieldName)
    {
        parent::__construct($fieldName);
        $this->setType('checkbox');
    }

    public function render()
    {
        $html = array();
        $index = 0;
        foreach ($this->getOptions() as $value => $label) {
            $this['id'] = $this->createId() . "_$index";
            $html[$index] = '<input ' . $this->attrsToString();
            if (in_array($value, (array) $this->getValue())) {
                $html[$index] .= ' checked="checked" ';
            }
            $html[$index++] .= ' value="' . $value . '" /> ' . $label . PHP_EOL;
        }
        return join($this->getSeparator(), $html);
    }
    
    protected function prepareAttrs()
    {
        $this->attrs['name'] = $this->formName . '[' . $this->getFieldName() . '][]';
        $this->attrs['type'] = $this->getType();
        unset($this->attrs['required']);
    }

}