<?php

namespace KumbiaPHP\Cache;

use KumbiaPHP\Cache\Cache;
use KumbiaPHP\Kernel\AppContext;

/**
 * Description of Service
 *
 * @author manuel
 */
class Service
{

    protected $appContext;

    function __construct(AppContext $app)
    {
        $this->appContext = $app;
    }

    public function get()
    {
        return \KumbiaPHP\Cache\Cache::driver($this->appContext);
    }

}