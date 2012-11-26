K2_Debug
========
Módulo para el debugueo de aplicaciónes en K2, permite ver las consultas SQL ejecutadas, ver los archivos incluidos, inspeccionar variales, etc...

Instalacion
-----------

Solo debemos descargar e instalar la lib en **vendor/K2/Debug** y registrarla en el [AppKernel](https://github.com/manuelj555/k2/blob/master/doc/app_kernel.rst):

```php

//archivo app/AppKernel.php

protected function registerModules()
{
    $modules = array(
        'KumbiaPHP'   => __DIR__ . '/../../vendor/kumbiaphp/kumbiaphp/src/',
        'Index'       => __DIR__ . '/modules/',
        ...
        'K2/Debug'   => __DIR__ . '/../../vendor/',
    );
    ...
}
```

Con esto ya hemos registrado el módulo en nuestra aplicación.