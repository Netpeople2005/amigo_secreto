<?php

namespace KumbiaPHP\Translation\Provider;

use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\Translation\Provider\ProviderInterface;

class Arrays implements ProviderInterface
{

    protected $messages = array();

    public function get($id, $locale)
    {
        $this->getMessages($locale);

        return isset($this->messages[$locale][$id]) ?
                $this->messages[$locale][$id] : false;
    }

    public function getMessages($locale)
    {
        if (!array_key_exists($locale, $this->messages)) {
            if (is_file($file = Kernel::getParam('app_dir') . "config/messages/$locale.php")) {
                $messages = require_once $file;
                $this->messages[$locale] = $messages;
            } else {
                $this->messages[$locale] = array();
            }
        }


        return $this->messages[$locale];
    }

}
