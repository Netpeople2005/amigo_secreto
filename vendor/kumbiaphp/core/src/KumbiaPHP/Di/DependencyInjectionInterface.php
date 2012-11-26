<?php

namespace KumbiaPHP\Di;

use KumbiaPHP\Di\Container\Container;

/**
 * Interface que define los métodos que deberá tener un inyector de dependencias.
 * @author manuel
 */
interface DependencyInjectionInterface
{

    /**
     * Crea una instancia de una clase.
     * 
     * Internamente le inyecta las dependencias que dicha clase necesite si
     * estan disponibles en el Container.
     * 
     * @param string $id nombre del servicio.
     * @param array $config configuracion de la clase a instanciar, de que servicos
     * depende, que parametros solicita, nombre de la clase, etc.
     * @return object devuelve la instancia creada
     * @throws IndexNotDefinedException 
     */
    public function newInstance($id, $config);

    /**
     * Establece el Container a usar por el inyector.
     * @param Container $container 
     */
    public function setContainer(Container $container);
}
