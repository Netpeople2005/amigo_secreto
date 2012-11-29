<?php

namespace KumbiaPHP\Form\Field;

use \ArrayAccess;
use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\Validation\ValidationBuilder;

/**
 *
 * @author manuel
 */
abstract class AbstractField implements ArrayAccess
{

    protected $formName;

    /**
     * Etiqueta para el campo del formulario
     * 
     * @var string 
     */
    protected $label = '';

    /**
     * Nombre del campo del formulario.
     *
     * @var string 
     */
    protected $fieldName;

    /**
     * Valor del campo del formulario
     *
     * @var string 
     */
    protected $value;

    /**
     * @var ValidationBuilder 
     */
    protected $validationBuilder;

    /**
     * Atributos html del campo.
     *
     * @var array 
     */
    protected $attrs = array();

    /**
     * Error de validación que puede tener el campo.
     * 
     * @var string 
     */
    protected $errors = NULL;

    /**
     * Tipo de campo a general, por defecto una caja de texto
     * @var type 
     */
    protected $type = 'text';

    /**
     * Constructor de la clase.
     *  
     * @param string $fieldName Nombre para el campo.
     */
    public function __construct($fieldName)
    {
        $this->setFieldName($fieldName);
    }

    public function setFormName($formName)
    {
        $this->formName = $formName;
        return $this->attrs(array('id' => $this->createId()));
    }

    public function setValidationBuilder(ValidationBuilder $vb)
    {
        $this->validationBuilder = $vb;
        return $this;
    }

    /**
     * Establece el nombre del campo.
     *
     * @param string $fieldName
     * 
     * @return Field 
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
        return $this;
    }

    /**
     * Devuelve el nombre del campo
     * 
     * @return string 
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Establece la etiqueta para el campo.
     *
     * @param string $label
     * @return Field 
     */
    public function setLabel($label)
    {
        if (Kernel::get('container')->has('translator')) {
            $this->label = Kernel::get('translator')->trans($label);
        } else {
            $this->label = $label;
        }
        return $this;
    }

    /**
     * Obtiene la etiqueta del campo.
     * 
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Establece el type para el campo.
     * 
     * @param string $type
     * @return Field 
     */
    protected function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Obtiene el tipo de campo.
     * 
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Establece el valor del campo.
     *
     * @param string $value
     * @return Field 
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Devuelve el valor del campo.
     * 
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Agrega un validación de campo requerido, es decir que no puede estar vacio.
     * 
     * @param string $message Mensaje a mostrar en caso de error al validar.
     * @return Field 
     */
    public function required($required = TRUE, $message = 'El campo {label} es requerido')
    {
        if ($required) {
            $this->validationBuilder->notNull($this->getFieldName(), array(
                'message' => $message
                    ), false);
            return $this->attrs(array('required' => 'required'));
        } else {
            $this->validationBuilder->remove('NotNull', $this->getFieldName());
            unset($this->attrs['required']);
            return $this;
        }
    }

    /**
     * Verifica si un campo es requerido.
     *
     * @return boolean 
     */
    public function isRequired()
    {
        return $this->validationBuilder->has(ValidationBuilder::NOT_NULL, $this->getFieldName());
    }

    /**
     * Devuelve las validaciones establecidas para el campo.
     * 
     * @return array 
     */
    public function getValidations()
    {
        return $this->validationBuilder;
    }

    /**
     * Establece atributos html para el campo.
     * 
     * @param array $attrs arreglo con claves => valor donde la clave es el nombre
     * del atributo y el value su contenido ó valor.
     * 
     * @return Field 
     */
    public function attrs(array $attrs)
    {
        $this->attrs = array_merge($this->attrs, $attrs);
        if (array_key_exists('value', $attrs)) {
            //el valor siempre debe quedar en la propiedad $_value de la clase
            $this->setValue(htmlspecialchars_decode($attrs['value'], ENT_COMPAT));
        }
        return $this;
    }

    /**
     * Devuelve el campo como html.
     * 
     * @return string 
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Prepara los atributos a imprimir en el html que representará al campo.
     * 
     * @return array 
     */
    protected function prepareAttrs()
    {
        $this->attrs(array(
            'name' => $this->formName . '[' . $this->getFieldName() . ']',
            'type' => $this->getType(),
        ));
        if (!is_array($this->getValue())) {
            $this->attrs(array(
                'value' => htmlspecialchars($this->getValue(), ENT_COMPAT),
            ));
        }
    }

    /**
     * Convierte el arreglo de atributos en un html para usar en el campo.
     * 
     * @return string 
     */
    protected function attrsToString()
    {
        $string = NULL;
        $this->prepareAttrs();
        foreach ($this->attrs as $attr => $value) {
            $string .= "$attr=\"$value\" ";
        }
        return $string;
    }

    /**
     * Devuelve el campo como html.
     *
     * @return string 
     */
    abstract public function render();

    public function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * Devuelve el mensaje de error, de existir el mismo.
     * 
     * @return string 
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Devuelve el mensaje de error, de existir el mismo.
     * 
     * @return string 
     */
    public function getError()
    {
        return count($this->errors) ? current($this->errors) : NULL;
    }

    /**
     * Verifica la existencia de un atributo en el campo.
     * 
     * @param string $offset
     * @return boolean 
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->attrs);
    }

    /**
     * Devuelve el valor de un atributo del campo, ó nulo si no existe el mismo.
     * 
     * @param string $offset
     * @return string|NULL
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->attrs[$offset];
        } else {
            if ($offset === 'label') {
                return $this->getLabel();
            } elseif ($offset === 'name') {
                return $this->getFieldName();
            } elseif ($offset === 'error') {
                return $this->getError();
            } else {
                return NULL;
            }
        }
    }

    /**
     * Establece un atributo para el campo.
     * 
     * @param string $offset atributo
     * @param string $value valor
     * @return Field 
     */
    public function offsetSet($offset, $value)
    {
        $this->attrs[$offset] = $value;
        return $this;
    }

    /**
     * Elimina ó remueve un atributo del campo.
     * 
     * @param $offset atributo a remover
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->attrs[$offset]);
        }
    }

    protected function createId()
    {
        return $this->formName . '_' . preg_replace('/(\[(.*)\])/i', '_$2', $this->getFieldName());
    }

    public function init()
    {
        
    }

}
