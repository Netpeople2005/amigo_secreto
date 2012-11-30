<?php

define('START_TIME', microtime(1));

//require_once __DIR__ . '/../app/kernel.min.php';
require_once __DIR__ . '/../app/AppKernel.php';

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Cache\AppCache;

$app = new AppKernel(false);

//$app = new AppCache($app);

$app->execute(new Request())->send();