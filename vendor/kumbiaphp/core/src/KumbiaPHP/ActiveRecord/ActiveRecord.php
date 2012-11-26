<?php

namespace KumbiaPHP\ActiveRecord;

use ActiveRecord\Model;
use KumbiaPHP\Kernel\Kernel;
use ActiveRecord\Config\Config;
use KumbiaPHP\Validation\Validator;
use KumbiaPHP\Validation\Validatable;
use KumbiaPHP\ActiveRecord\Config\Reader;
use KumbiaPHP\ActiveRecord\Validation\ValidationBuilder;

/**
 * Description of ActiveRecord
 *
 * @author maguirre
 */
class ActiveRecord extends Model implements Validatable
{

    /**
     * 
     * @var Validation\ValidationBuilder;
     */
    protected $validation;

    /**
     * Errores de Validación
     * @var array 
     */
    protected $errors;

    public function getValidations()
    {
        if (!$this->validation) {
            $this->validation = new ValidationBuilder();
            /* @var $attribute \ActiveRecord\Metadata\Attribute */
            foreach ($this->metadata()->getAttributes() as $field => $attribute) {
                if (true === $attribute->notNull && !$attribute->PK) {
                    $this->validation->notNull($field, array(
                        'message' => "El Campo {field} no puede ser Nulo",
                        'field' => $attribute->alias,
                    ));
                }
                if (null !== $attribute->length && is_numeric($attribute->length)) {
                    $this->validation->maxLength($field, array(
                        'message' => "El Campo {field} no puede ser mayor a {max} caracteres",
                        'max' => $attribute->length,
                        'field' => $attribute->alias,
                    ));
                }
                if (true === $attribute->unique) {
                    $this->validation->unique($field, array(
                        'message' => "El Valor especificado para el Campo {field} ya existe en el Sistema",
                        'field' => $attribute->alias,
                    ));
                }
            }
        }
        return $this->validations($this->validation);
    }

    protected function validate($update = FALSE)
    {
        if ($update) {
            return Kernel::get('validator')->validateOnUpdate($this);
        } else {
            return Kernel::get('validator')->validate($this);
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($field, $message)
    {
        $this->errors[$field] = $message;
    }

    /**
     * método que implementarán los modelos para crear las validaciones.
     * @param ValidationBuilder $builder 
     * @return ValidationBuilder
     */
    protected function validations(ValidationBuilder $builder)
    {
        return $builder;
    }

}

if (!Config::initialized()) {
    //si no está inicializada la configuración que usa el Active Record,
    //lo inicializamos.
    Reader::readDatabases();
    \ActiveRecord\DbPool\DbPool::setAttributes(array(
        \PDO::ATTR_STATEMENT_CLASS => array('KumbiaPHP\\ActiveRecord\\PDOStatement')
    ));
}
