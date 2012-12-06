<?php

namespace KumbiaPHP\Di\Container;

/**
 * Define una interface que debe implementar el contenedor de servicios del FW
 *
 * @author manuel
 */
interface ContainerInterface extends \ArrayAccess
{

    /**
     * Devuelve la instancia del servicio si existe
     * @param string $id nombre del servicio 
     * @return object|NULL
     */
    public function get($id);

    /**
     * Verifica la existencia de un servicio en el contenedor
     * @param string $id nombre del servicio
     * @return boolean 
     */
    public function has($id);

    /**
     * Verifica si un servicio ya ha sido creado por el contenedor.
     * @param string $id nombre del servicio
     * @return boolean 
     */
    public function hasInstance($id);

    /**
     * Devuelve el valor de un parametro si existe
     * @param string $id nombre del parametro
     * @return mixed|NULL 
     */
    public function getParameter($id);

    /**
     * Verifica la existencia de un parametro en el contenedor
     * @param string $id nombre del parametro
     * @return boolean 
     */
    public function hasParameter($id);

    /**
     * Establece un valor para un parametro
     * @param string $id nombre del parametro
     * @param mixed $value valor del parametro
     * @return ContainerInterface
     */
    public function setParameter($id, $value);

    /**
     * Devuelve las definiciones de los servicios
     * @return array 
     */
    public function getDefinitions();
}
