<?php

namespace KumbiaPHP\Validation;

class ValidationBuilder implements \Serializable
{

    const NOT_NULL = 'KumbiaPHP\\Validation\\Validators\\NotNull';
    const INT = 'KumbiaPHP\\Validation\\Validators\\Integer';
    const MAX_LENGTH = 'KumbiaPHP\\Validation\\Validators\\MaxLength';
    const MIN_LENGTH = 'KumbiaPHP\\Validation\\Validators\\MinLength';
    const LENGTH_BETWEEN = 'KumbiaPHP\\Validation\\Validators\\LengthBetween';
    const IN_LIST = 'KumbiaPHP\\Validation\\Validators\\InList';
    const DATE = 'KumbiaPHP\\Validation\\Validators\\Date';
    const RANGE = 'KumbiaPHP\\Validation\\Validators\\Range';
    const URL = 'KumbiaPHP\\Validation\\Validators\\Url';
    const EQUAL_TO = 'KumbiaPHP\\Validation\\Validators\\EqualTo';

    protected $valitations = array();

    public function set($type, $field, array $params = array(), $replace = true)
    {
        if ($replace || !$this->has($type, $field)) {
            $this->valitations[$type][$field] = $params;
        }
        return $this;
    }

    public function notNull($field, array $params = array(), $replace = true)
    {
        return $this->set(self::NOT_NULL, $field, $params, $replace);
    }

    public function int($field, array $params = array(), $replace = true)
    {
        return $this->set(self::INT, $field, $params, $replace);
    }

    public function maxLength($field, array $params = array(), $replace = true)
    {
        return $this->set(self::MAX_LENGTH, $field, $params, $replace);
    }

    public function minLength($field, array $params = array(), $replace = true)
    {
        return $this->set(self::MIN_LENGTH, $field, $params, $replace);
    }

    public function lengthBetween($field, array $params = array(), $replace = true)
    {
        return $this->set(self::LENGTH_BETWEEN, $field, $params, $replace);
    }

    public function inList($field, array $params = array(), $replace = true)
    {
        return $this->set(self::IN_LIST, $field, $params, $replace);
    }

    public function date($field, array $params = array(), $replace = true)
    {
        return $this->set(self::DATE, $field, $params, $replace);
    }

    public function range($field, array $params = array(), $replace = true)
    {
        return $this->set(self::RANGE, $field, $params, $replace);
    }

    public function url($field, array $params = array(), $replace = true)
    {
        return $this->set(self::URL, $field, $params, $replace);
    }

    public function equalTo($field, array $params = array(), $replace = true)
    {
        return $this->set(self::EQUAL_TO, $field, $params, $replace);
    }

    public function has($type, $field)
    {
        return isset($this->valitations[$type]) && isset($this->valitations[$type][$field]);
    }

    public function remove($field, $type = NULL)
    {
        if (NULL !== $type) {
            if ($this->has($type, $field)) {
                unset($this->valitations[$type][$field]);
            }
        } else {
            foreach ($this->valitations as $type => $fields) {
                if (isset($fields[$field])) {
                    $this->remove($field, $type);
                }
            }
        }
    }

    public function getValidations()
    {
        return $this->valitations;
    }

    public function serialize()
    {
        return null;
    }

    public function unserialize($serialized)
    {
        $this->valitations = array();
    }

}