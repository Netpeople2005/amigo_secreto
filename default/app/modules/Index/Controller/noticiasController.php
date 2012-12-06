<?php
/**
 * Created by JetBrains PhpStorm.
 * User: apatino
 * Date: 12/6/12
 * Time: 11:30 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Index\Controller;
use KumbiaPHP\Kernel\Controller\Controller;
use Index\Model\Noticias;
use KumbiaPHP\Form\Form;

class noticiasController extends Controller
{
    public function index_action(){
        Noticias::createQuery()->order('id DESC');
        $this->noticias = Noticias::findAll();
    }

    public function crear_noticia_action(){
        $form = new Form($not = new Noticias());
        $form->add('noticia', 'textarea')->setLabel('Ingrese la Noticia:')->required();
        $this->form = $form;

        if ($this->getRequest()->isMethod('post')) {
            $not->hora = date('Y-m-d H:i:s');
            if($form->bindRequest($this->getRequest())->isValid()){
                if ($not->save()) {
                    $this->get('flash')->success("Noticia Registrada!!!");
                    $form->setData(array());
                } else {
                    $this->get('flash')->error($not->getErrors());
                }
            }else{
                $this->get('flash')->error($form->getErrors());
            }
        }
    }

    public function recargar_action(){
        $recar = Noticias::findAll();
        return new \KumbiaPHP\Kernel\JsonResponse($recar);
    }
}
