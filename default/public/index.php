<?php

define('START_TIME', microtime(1));

//require_once '../app/kernel.min.php';
require_once '../app/AppKernel.php';

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Cache\AppCache;

$app = new AppKernel(false);

//$app = new AppCache($app);

$app->execute(new Request())->send();