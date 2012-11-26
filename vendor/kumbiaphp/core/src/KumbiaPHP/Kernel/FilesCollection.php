<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\File;
use KumbiaPHP\Kernel\Collection;

class FilesCollection extends Collection
{

    public function __construct()
    {
        foreach ((array) $_FILES as $name => $data) {
            $this->set($name, new File($data));
        }
    }

}