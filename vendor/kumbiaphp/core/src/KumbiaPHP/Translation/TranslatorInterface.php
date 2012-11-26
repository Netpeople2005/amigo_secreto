<?php

namespace KumbiaPHP\Translation;

interface TranslatorInterface
{

    /**
     * Realiza la traducción de un texto
     * @param string $text
     * @param array $params
     * @param string $locale 
     */
    public function trans($text, array $params = array(), $locale = null);
}
