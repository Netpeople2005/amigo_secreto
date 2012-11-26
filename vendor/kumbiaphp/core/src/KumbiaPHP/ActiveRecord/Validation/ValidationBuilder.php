<?php

namespace KumbiaPHP\ActiveRecord\Validation;

use KumbiaPHP\Validation\ValidationBuilder as BaseBuilder;

/**
 *
 * @author manuel
 */
class ValidationBuilder extends BaseBuilder
{

    const DEFAULT_FIELD = 'KumbiaPHP\\ActiveRecord\\Validation\\Validators\\DefaultField';
    const PRIMARY = 'KumbiaPHP\\ActiveRecord\\Validation\\Validators\\Primary';
    const UNIQUE = 'KumbiaPHP\\ActiveRecord\\Validation\\Validators\\Unique';

    //acá irán las validaciones del AR
    public function defaultField($field, array $params = NULL)
    {
        return $this->add(self::DEFAULT_FIELD, $field, $params);
    }

    public function unique($field, array $params = NULL)
    {
        return $this->add(self::UNIQUE, $field, $params);
    }

    public function primary($field, array $params = NULL)
    {
        return $this->add(self::PRIMARY, $field, $params);
    }

}

