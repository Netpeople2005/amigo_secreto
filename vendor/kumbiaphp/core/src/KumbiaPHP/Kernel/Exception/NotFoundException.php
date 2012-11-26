<?php

namespace KumbiaPHP\Kernel\Exception;

/**
 * Description of NotFoundException
 *
 * @author manuel
 */
class NotFoundException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }

}