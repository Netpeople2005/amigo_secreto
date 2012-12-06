<?php

namespace Index\Controller;

use KumbiaPHP\Form\Form;
use Index\Model\Usuarios;
use KumbiaPHP\Upload\Upload;
use Index\Model\EquiposRegistrados;
use KumbiaPHP\Kernel\Controller\Controller;

class indexController extends Controller
{

    public function index_action()
    {
        return $this->getRouter()->forward('regalos/');
    }

    public function inicio_action()
    {
        if (null === $this->get('security')->getToken('clave')) {
            $this->get('flash')->warning("Debes Crear Tu Contraseña...!!!");
            return $this->getRouter()->forward('registro/cambiar_clave');
        }
        
        return $this->getRouter()->forward('regalos/');
    }

    public function foto_action()
    {
        $personajes = Usuarios::findAll('array');

        $form = new Form('cambio_foto');

        $form->add('personaje', 'select')
                ->setOptionsFromResultset($personajes, 'id', 'personaje')
                ->setDefault('- Seleccionar -')
                ->setLabel('Personaje')
                ->required();

        $form->add('imagen', 'file')
                ->setLabel('Nueva Imagen')
                ->required();

        if ($this->getRequest()->isMethod('post')) {
            if ($form->bindRequest($this->getRequest())->isValid()) {
                //cambiar esto para usar la lib Upload y poder validar el tamaño
                //y extension de la imagen.
                $upload = Upload::factory($this->getRequest(), array('cambio_foto', 'imagen'), 'Image');

                $path = dirname($this->container['app_dir']) .'/public/img/perfiles/';

                $upload->setMinWidth(150);
                $upload->setMaxWidth(150);
                $upload->setMinHeight(180);
                $upload->setMaxHeight(180);
                $upload->allowOverwrite(true);
                $upload->setPath($path);

                if ($upload->save()) {
                    $personaje = Usuarios::findByPK($form['personaje']->getValue());
                    $personaje->imagen = 'perfiles/' . $upload->getFile()->getName();
                    if($personaje->save()){
                        $this->get('flash')->success("Imagen subida con exito");                        
                    }else{
                        $this->get('flash')->error($personaje->getErrors());
                    }
                }else{
                    $this->get('flash')->error($upload->getErrors());
                }
            }
        }

        $this->form = $form;
    }

}