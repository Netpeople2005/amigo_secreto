<?php

namespace KumbiaPHP\Validation;

use KumbiaPHP\Validation\ValidationBuilder;

/**
 *
 * @author manuel
 */
interface Validatable
{

    /**
     * Este metodo es llamado por el validador para obtener
     * las reglas de validación a ejecutar.
     */
    public function getValidations();

    public function addError($index, $message);

    public function getErrors();
}

