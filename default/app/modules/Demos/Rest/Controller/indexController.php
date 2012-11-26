<?php

namespace Demos\Rest\Controller;

use KumbiaPHP\Kernel\Controller\Controller;
use KumbiaPHP\Kernel\JsonResponse;

/**
 * Ejemplo de un controlador REST FULL
 * 
 * Este controlador puede manejar peticiones de tipo rest
 *
 * @author maguirre
 */
class indexController extends Controller
{

    /**
     * Este filtro se ejecuta antes de la llamada a cualquier metodo del controlador
     * 
     * @return null|string
     * 
     * este filtro puede ó no retornar nada, ó retornar una cadena con el nombre
     * de la nueva acción a ejecutar en el controlador. 
     */
    protected function beforeFilter()
    {
        //aqui le decimos que ejecute la accion que tenga el nombre
        //del método de petición.
        return strtolower($this->getRequest()->getMethod()) . '_action';
    }

    /**
     * Este método es llamado en las peticiones de tipo GET
     * ya que el filtro reescribe la acción a llamar dependiendo del metodo de la peticion
     * 
     * @return \KumbiaPHP\Kernel\Response 
     */
    public function get_action()
    {
        //creamos un arreglo de ejemplo para imprimirlo como json
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "GET",
        );
        /*
         * retornamos un objeto RESPONSE donde su contenido es un json del areglo
         * el status es 200 y el content type será application/json
         */
        return new JsonResponse($data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Aplica los que para el metodo get
     * 
     * @return \KumbiaPHP\Kernel\Response 
     */
    public function post_action()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "POST",
        );
        return new JsonResponse($data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Aplica los que para el metodo get
     * 
     * @return \KumbiaPHP\Kernel\Response 
     */
    public function put_action()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "PUT",
        );
        return new JsonResponse($data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Aplica los que para el metodo get
     * 
     * @return \KumbiaPHP\Kernel\Response 
     */
    public function delete_action()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "DELETE",
        );
        return new JsonResponse($data, 200, array('Content-Type' => 'application/json'));
    }

}
