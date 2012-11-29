<?php

namespace KumbiaPHP\Validation;

use KumbiaPHP\Validation\Validatable;
use KumbiaPHP\Validation\ValidationBuilder;
use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Validation\Validators\ValidatorBase;

/**
 * Description of Validator
 *
 * @author manuel
 */
class Validator
{

    public function __construct(ContainerInterface $container)
    {
        ValidatorBase::setContainer($container);
    }

    public function validate(Validatable $object, ValidationBuilder $builder = null)
    {
        $builder || $builder = $object->getValidations();

        if (!$builder instanceof ValidationBuilder) {
            throw new \LogicException(sprintf("El método\"getValidations\" de la clase \"%s\" debe devolver un objeto ValidationBuilder", get_class($object)));
        }

        return $this->execute($object, $builder->getValidations());
    }

    public function validateOnUpdate(Validatable $object, ValidationBuilder $builder = null)
    {
        $builder || $builder = $object->getValidations();

        if (!$builder instanceof ValidationBuilder) {
            throw new \LogicException(sprintf("El método\"getValidations\" de la clase \"%s\" debe devolver un objeto ValidationBuilder", get_class($object)));
        }
        return $this->execute($object, $builder->getValidations(), true);
    }

    protected function execute(Validatable $object, array $validations, $update = false)
    {
        if (!count($validations)) {
            //si no se debe validar nada.
            return true;
        }
        $valid = true;
        foreach ($validations as $classValidation => $fields) {
            foreach ($fields as $field => $params) {
                if (false === call_user_func_array(array($classValidation, 'validate'), array(
                            $object, $field, $params, $update))) {
                    //agregamos el error al objeto.
                    $object->addError($field, ValidatorBase::getLastError());
                    $valid = false;
                }
            }
        }
        return $valid;
    }

}