<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 * Interface base para el kernel del FW
 * 
 * Contiene los metodos minimos a implementar por un kernel para correr
 * una petición.
 *
 * @author manuel
 */
interface KernelInterface
{
    
    const MASTER_REQUEST = 1;
    const SUB_REQUEST = 2;

    /**
     * Metodó que ejecuta todo el proceso de la ejecucion de la petición.
     * @param Request $request objeto que contiene toda la info de la petición 
     * @param int $type indica si la petición es la original ó es una sub petición. 
     * @return Response objeto respuesta
     * @throws \LogicException excepcion si no se puede devolver una respuesta
     */
    public function execute(Request $request, $type = KernelInterface::MASTER_REQUEST);
    
    /**
     * Devuelve un servicio del container, se usa en casos especiales donde
     * no sea posible pasarlo a travez del inyector de dependencias,
     * por ejemplo en el active record.
     * 
     * Por favor evitar en los posible su uso
     * 
     * @return object
     */
    public static function get($service);
    
    /**
     * Devuelve un parametro del contenedor 
     */
    public static function getParam($param);
}
