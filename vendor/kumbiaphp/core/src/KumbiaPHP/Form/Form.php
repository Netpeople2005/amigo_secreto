<?php

namespace KumbiaPHP\Form;

use \ArrayAccess;
use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Validation\Validatable;
use KumbiaPHP\ActiveRecord\ActiveRecord;
use KumbiaPHP\Form\Exception\FormException;
use KumbiaPHP\Validation\ValidationBuilder;
use KumbiaPHP\Form\Field\AbstractField as Field;
use KumbiaPHP\ActiveRecord\Validation\ValidationBuilder as ValidationAR;

/**
 * 
 *
 * @author programador.manuel@gmail.com
 */
class Form implements ArrayAccess, Validatable
{

    protected $name;

    /**
     * Campos (Elementos) del formulario.
     * 
     * @var array 
     */
    protected $fields = array();

    /**
     * @var ActiveRecord;
     */
    protected $model = NULL;

    /**
     * Url a la que apuntar� el Form
     *
     * @var string 
     */
    protected $action = NULL;

    /**
     * Método a usar para el envio del form
     * 
     * @var string 
     */
    protected $method = 'post';

    /**
     * Atributos html del formulario.
     * 
     * @var array 
     */
    protected $attrs = array();

    /**
     * Errores de validación del formulario.
     * 
     * @var array 
     */
    protected $errors = array();

    /**
     * @var \KumbiaPHP\Validation\Validator
     * @var type 
     */
    protected static $validator;

    /**
     * @var ValidationBuilder 
     */
    protected $validationBuilder;

    /**
     * Constructor de la clase.
     * 
     * Más Adelante podrá recibir un objeto Active Record, y crear las 
     * validaciones a partir de la lectura de los requerimientos del mismo.
     * por lo que estará validado con html y con la lib FormBuilder.
     * 
     * @param ActiveRecord|string $model modelo AR ó nombre del form
     * @param boolean $createFields indica si se crearan los campos a partir del modelo.
     */
    final public function __construct($model = NULL, $createFields = FALSE)
    {
        if ($model instanceof ActiveRecord) {
            if (!($this->validationBuilder = $model->getValidations()) instanceof ValidationBuilder) {
                throw new \LogicException(sprintf("El método\"validations\" de la clase \"%s\" debe devolver un objeto ValidationBuilder", get_class($model)));
            }
            $this->name = strtolower(basename(get_class($model)));
            $this->model = $model;
            if ($createFields) {
                $this->initFromModel($model);
            } else {
                $this->init();
                $this->initExtrasFromModel($model);
            }
        } elseif (is_string($model)) {
            $this->validationBuilder = new ValidationBuilder();
            $this->name = $model;
            $this->init();
        } else {
            throw new FormException("El valor para el argumento \$model debe ser una instancia de ActiveRecord ó un string, se envió: " . gettype($model));
        }
    }

    /**
     * Devuelve el nombre del formulario
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Establece un nombre para el formulario
     * @param string $name
     * @return \KumbiaPHP\Form\Form 
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @param string|\KumbiaPHP\Form\Field\Field $fieldName
     * @param string $type
     * @param array $options
     * @return \KumbiaPHP\Form\Field\Text|\KumbiaPHP\Form\Field\AbstractChoice
     * @throws Exception 
     */
    public function add($fieldName, $type = 'text', array $options = array())
    {
        if ($fieldName instanceof Field\Field) {
            return $this->_add($fieldName);
        } elseif (is_string($fieldName)) {
            $type = 'KumbiaPHP\\Form\\Field\\' . ucwords($type);
            if (!class_exists($type)) {
                throw new FormException("No existe el tipo de Campo <b>$type</b> en la Lib Form");
            }

            return $this->_add(new $type($fieldName));
        } else {
            throw new FormException('No se reconoce el valor del atributo $field: ' . $field);
        }
    }

    /**
     * Agrega un elemento al formulario.
     *
     * @param Field $field elemento a agregar.
     * 
     * @return Field objeto que se cre�.
     */
    protected function _add(Field $field)
    {
        $index = $field->setFormName($this->getName())
                ->setValidationBuilder($this->validationBuilder)
                ->getFieldName();
        $field->init(); //inicializaciones especiales.
        $this->fields[$index] = $field;
        if ($field instanceof Field\File) {
            $this->attrs(array('enctype' => 'multipart/form-data'));
        }
        if (($this->model instanceof ActiveRecord ) && isset($this->model->{$index})) {
            $field->setValue($this->model->{$index});
        }
        return $field;
    }

    /**
     * Crea la etiqueta de apertura para el formulario
     * @param array $attrs atributos html para el form
     * 
     * @return string 
     */
    public function open($attrs = array())
    {
        $html = "<form " . $this->attrs($attrs)->attrsToString() . ">" . PHP_EOL;
        foreach ($this->fields as $field) {
            if ($field->getType() === 'hidden') {
                $html .= $field->render() . PHP_EOL;
                $this->removeField($field->getFieldName()); //eliminadmos el campo del form, porque ya no se necesitará
            }
        }
        return $html;
    }

    /**
     * Crea la etiqueta de cierre para el formulario
     * 
     * @return string 
     */
    public function close()
    {
        return "</form>";
    }

    /**
     * Establece la Acción para el formulario.
     * 
     * @param string $action
     * @return FormBuilder 
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Devuelve la acción a la que apunta el formulario actualmente.
     * 
     * @return string 
     */
    public function getAction()
    {
        if ($this->action) {
            return Kernel::get('app.context')->createUrl($this->action);
        } else {
            return Kernel::get('app.context')->getCurrentUrl(true);
        }
    }

    /**
     * Establece el método de la petición
     *
     * @param string $method
     * @return FormBuilder 
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Devuelve el M�todo de la petici�n
     * 
     * @return string 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Establece el los errores de validación
     *
     * @param array $errors
     * @return FormBuilder 
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Devuelve un arreglo con los mensajes de error de los campos invalidos del
     * formulario.
     * 
     * @return array 
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Establece atributos html para el form.
     * 
     * @param array $attrs arreglo con claves => valor donde la clave es el nombre
     * del atributo y el value su contenido � valor.
     * 
     * @return FormBuilder
     */
    public function attrs(array $attrs)
    {
        $this->attrs = array_merge($this->attrs, $attrs);
        return $this;
    }

    /**
     * Devuelve un campo del formulario previamente creado
     *
     * @param string $element Nombre del campo a obtener.
     * 
     * @return Field objeto que se encuentra en el form  � 
     * NULL si el elemento no existe.
     */
    public function getField($element)
    {
        if (array_key_exists($element, $this->fields)) {
            return $this->fields[$element];
        } else {
            return NULL;
        }
    }

    /**
     * Devuelve los elementos actuales del formulario
     * @return array 
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Remueve un campo del formulario previamente creado
     *
     * @param string $element Nombre del campo a remover.
     * 
     * @return FormBuilder
     */
    public function removeField($element)
    {
        if (array_key_exists($element, $this->fields)) {
            unset($this->fields[$element]);
            $this->validationBuilder->remove($element);
        }
        return $this;
    }

    /**
     * Devuelve el formulario completo con los elementos creados, todo
     * en formato HTML.
     * 
     * @return string 
     */
    public function render()
    {
        $string = $this->open() . PHP_EOL;
        $string .= "<ul class=\"form_errors\">";
        foreach ($this->getErrors() as $e) {
            $string .= "<li>$e</li>";
        }
        $string .= "</ul><ul>";
        foreach ($this->fields as $field) {
            if ($field instanceof \KumbiaPHP\Form\Field\Hidden) {
                continue;
            } elseif ($field instanceof \KumbiaPHP\Form\Field\Check ||
                    $field instanceof \KumbiaPHP\Form\Field\Radio) {
                $string .= "<li>" . $field['label'] . PHP_EOL;
                $string .= $field . "</li>" . PHP_EOL;
            } else {
                $string .= "<li><label>" . $field['label'] . PHP_EOL;
                $string .= $field . "</label></li>" . PHP_EOL;
            }
        }
        if (Kernel::get('container')->has('translator')) {
            $string .= "<li>" . $this->add('submitSend', 'submit')
                            ->setValue(Kernel::get('translator')->trans('Enviar')) . " " . PHP_EOL;
            $string .= $this->add('buttonReset', 'reset')
                            ->setValue(Kernel::get('translator')->trans('Resetear')) . "</li>" . PHP_EOL;
        } else {
            $string .= "<li>" . $this->add('submitSend', 'submit')->setValue('Enviar') . " " . PHP_EOL;
            $string .= $this->add('buttonReset', 'reset')->setValue('Resetear') . "</li>" . PHP_EOL;
        }
        return $string . "</ul>" . PHP_EOL . $this->close() . PHP_EOL;
    }

    /**
     * Verifica si los valores de los elementos del formulario son validos.
     * 
     * @return boolean 
     */
    public function isValid()
    {
        if ($this->model instanceof ActiveRecord
                && isset($this->model->{$this->model->metadata()->getPK()})
                && $this->model->exists()) {
            return Kernel::get('validator')->validateOnUpdate($this);
        } else {
            return Kernel::get('validator')->validate($this);
        }
    }

    /**
     * Establece los valores para los elementos del formulario.
     * 
     * @param array $data arreglo con los datos a pasar
     * 
     * @return Form delvuelve el mismo objeto.
     */
    public function setData(array $data)
    {
        /* @var $field Field */
        if ($this->model instanceof ActiveRecord) {
            foreach ($this->fields as $fieldName => $field) {
                $field->setValue(isset($data[$fieldName]) ? $data[$fieldName] : NULL);
                $this->model->{$fieldName} = $field->getValue();
            }
        } else {
            foreach ($this->fields as $fieldName => $field) {
                $field->setValue(isset($data[$fieldName]) ? $data[$fieldName] : NULL);
            }
        }
        return $this;
    }

    /**
     * Establece los valores para los elementos del formulario por medio del
     * objeto Request.
     * 
     * @param Request $request instancia de la petición actual.
     * 
     * @return Form delvuelve el mismo objeto.
     */
    public function bindRequest(Request $request)
    {
        return $this->setData($request->get($this->name, array()));
    }

    /**
     * Devuelve un arreglo con los valores de los campos del formulario.
     *
     * @return array|ActiveRecord 
     */
    public function getData()
    {
        if ($this->model instanceof ActiveRecord) {
            return $this->model;
        } else {
            $values = array();
            foreach ($this->fields as $fieldName => $field) {
                $values[$fieldName] = $field->getValue();
            }
            return $values;
        }
    }

    /**
     * Devuelve el formulario completo con los elementos creados.
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Verifica la existencia de un campo en el formulario.
     * 
     * @param string $offset nombre del campo a verificar
     * @return boolean devuelve TRUE si el campo existe. 
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->fields);
    }

    /**
     * Devuelve un campo del formulario previamente creado
     *
     * @param string $offset nombre del campo a obtener
     * 
     * @return Field|array objeto que se encuentra en el form  � 
     * NULL si el elemento no existe.
     */
    public function offsetGet($offset)
    {
        return $this->getField($offset);
    }

    /**
     * Asigna atributos a un campo del formulario.
     *
     * @param string $offset nombre del campo
     * @param array|string $value si es un arreglo, ser�n atributos del 
     * formulario, mientras que si es una cadena, es el valor para el elemento.
     */
    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            if (is_array($value)) {
                return $this->getField($offset)->attrs($value);
            } else {
                return $this->getField($offset)->setValue($value);
            }
        }
    }

    /**
     * Elimina un elemento del formulario.
     *
     * @param string $offset nombre del campo
     */
    public function offsetUnset($offset)
    {
        return $this->removeField($offset);
    }

    /**
     * Prepara los atributos a imprimir en el html que representar� al form.
     * 
     * @return array 
     */
    protected function prepareAttrs()
    {
        $this->attrs(array(
            'action' => $this->getAction(),
            'method' => $this->getMethod(),
        ));
        return $this->attrs;
    }

    /**
     * Convierte el arreglo de atributos en un html para usar en el form.
     * 
     * @return string 
     */
    protected function attrsToString()
    {
        $string = NULL;
        foreach ($this->prepareAttrs() as $attr => $value) {
            $string .= "$attr=\"$value\" ";
        }
        return $string;
    }

    protected function init()
    {
        
    }

    private function initFromModel(ActiveRecord $model)
    {
        /* @var $attribute \ActiveRecord\Metadata\Attribute */
        foreach ($model->metadata()->getAttributes() as $fieldName => $attribute) {
            if ($attribute->PK && $attribute->autoIncrement) {
                $field = $this->add($fieldName, 'hidden');
            } else {
                $field = $this->add($fieldName)
                        ->setLabel($attribute->alias);
            }
            if (isset($model->{$fieldName})) {
                $field->setValue($model->{$fieldName});
            }
        }
        $this->initExtrasFromModel($model);
    }

    private function initExtrasFromModel(ActiveRecord $model)
    {
        /* @var $attribute \ActiveRecord\Metadata\Attribute */
        foreach ($model->metadata()->getAttributes() as $fieldName => $attribute) {
            if (($field = $this->getField($fieldName)) instanceof Field) {//si se creó el elemento
                if (true === $attribute->notNull && !$attribute->PK && !$attribute->default) {
                    $field->required();
                }
                if (null !== $attribute->length && is_numeric($attribute->length)) {
                    if (method_exists($field, 'maxLength')) {//se debe verificar si el objeto Field permite esta validación.
                        $field->maxLength($attribute->length);
                    }
                }
                if (true === $attribute->unique) {
                    $this->validationBuilder->add(ValidationAR::UNIQUE, $fieldName, array(
                        'message' => "El Valor especificado para el Campo {$field->getLabel()} ya existe en el Sistema"
                    ));
                }
                if (null !== $attribute->default && null === $field->getValue()) {
                    $field->setValue($attribute->default);
                }
            }
        }
    }

    public function addError($field, $message)
    {
        if (isset($this[$field])) {
            $this[$field]->addError($message);
        }
        $this->errors[] = $message;
        return $this;
    }

    public function getValidations()
    {
        return $this->validationBuilder;
    }

}