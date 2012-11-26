<?php

namespace Demos\SubiendoArchivos\Controller;

use KumbiaPHP\Kernel\Controller\Controller;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Upload\Upload;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class indexController extends Controller
{

    public function index_action()
    {
        if ($this->getRequest()->isMethod('POST')) {

            if ($this->getRequest()->files->has('imagen')) {
                $file = Upload::factory($this->getRequest(), 'imagen');
                if ($file->save(uniqid())) {
                    $this->get('flash')->success("El archivo se subió con exito...!!!");
                } else {
                    $this->get('flash')->error($file->getErrors());
                }
            }
        }
    }

}