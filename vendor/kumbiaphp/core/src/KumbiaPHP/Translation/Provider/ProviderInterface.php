<?php

namespace KumbiaPHP\Translation\Provider;

/**
 *
 * @author maguirre
 */
interface ProviderInterface
{

    public function get($id, $locale);

    public function getMessages($locale);
}
