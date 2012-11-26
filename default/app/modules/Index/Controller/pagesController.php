<?php

namespace Index\Controller;

use KumbiaPHP\Kernel\Controller\Controller;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class pagesController extends Controller
{

    protected $limitParams = FALSE;

    public function show_action()
    {
        $this->setView(implode('/', $this->parameters));
    }

}