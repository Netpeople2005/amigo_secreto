<?php

namespace KumbiaPHP\Translation;

use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\Translation\TranslatorInterface;
use KumbiaPHP\Translation\Provider\ProviderInterface;

class Translator implements TranslatorInterface
{

    /**
     *
     * @var MessagesInterface 
     */
    protected $messages;

    public function __construct()
    {
        $provider = Kernel::getParam('translator.provider');

        if ('@' === $provider[0]) {
            $this->messages = Kernel::get(substr($provider, 1));
            if (!$this->messages instanceof ProviderInterface) {
                $class = get_class($this->messages);
                throw new \LogicException("La clase {$class} debe implementar la Interfaz KumbiaPHP\\Translation\\Provider\\ProviderInterface");
            }
        } else {
            $providerClassName = 'KumbiaPHP\\Translation\\Provider\\' . ucfirst($provider);
            $this->messages = new $providerClassName();
        }
    }

    public function trans($text, array $params = array(), $locale = null)
    {
        //obtenemos el locale actual si no se especifica
        $locale || $locale = Kernel::get('request')->getLocale();

        if (false === $translation = $this->messages->get($text, $locale)) {
            $translation = $text;
        }

        return vsprintf($translation, $params);
    }

}