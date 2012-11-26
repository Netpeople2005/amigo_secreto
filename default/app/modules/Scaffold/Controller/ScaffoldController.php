<?php

namespace Scaffold\Controller;

use \KumbiaPHP\Form\Form;
use KumbiaPHP\ActiveRecord\ActiveRecord;
use KumbiaPHP\Kernel\Controller\Controller;

/**
 * Description of ScaffoldController
 *
 * @author manuel
 */
abstract class ScaffoldController extends Controller
{

    /**
     *
     * @var ActiveRecord 
     */
    public $model;
    public $scaffold = 'kumbia';

    abstract protected function beforeFilter();

    public function index_action($page = 1)
    {
        $this->checkModel();

        $this->paginator = $this->model->paginate($page);
    }

    public function ver_action($id)
    {
        $this->checkModel();

        $this->model = $this->model->findByPK((int) $id);
    }

    public function crear_action()
    {
        $this->checkModel();

        $this->form = new Form($this->model, true);

        if ($this->getRequest()->isMethod('POST')) {
            if ($this->form->bindRequest($this->getRequest())->isValid()) {
                if ($this->form->getData()->save()) {
                    $this->get('flash')->success("El registro fué exitoso");
                    return $this->getRouter()->toAction('index');
                }
            }
        }
    }

    public function editar_action($id)
    {
        $this->checkModel();
        $this->setView('crear');

        if (!$model = $this->model->findByPK((int) $id)) {
            $this->renderNotFound("No existe el Registro");
        }

        $this->form = new Form($model, true);

        if ($this->getRequest()->isMethod('POST')) {
            if ($this->form->bindRequest($this->getRequest())->isValid()) {
                if ($this->form->getData()->save()) {
                    $this->get('flash')->success("El Guardado fué exitoso");
                    return $this->getRouter()->toAction('index');
                }
            }
        }
    }

    public function borrar_action($id)
    {
        $this->checkModel();

        if (!$model = $this->model->findByPK((int) $id)) {
            $this->renderNotFound("No existe el Registro");
        }

        if ($model->deleteByPK((int) $id)) {
            $this->get('flash')->success("El Registro fué Eliminado");
        } else {
            $this->get('flash')->error("No se pudo eliminar el registro");
        }

        return $this->getRouter()->toAction('index');
    }

    private function checkModel()
    {
        if (!$this->model instanceof ActiveRecord) {
            throw new \LogicException("el Atributo \"model\" debe ser una instancia de un objeto ActiveRecord");
        }
    }

}