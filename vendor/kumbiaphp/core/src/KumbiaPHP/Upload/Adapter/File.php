<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * @category   Kumbia
 * @package    Upload
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace KumbiaPHP\Upload\Adapter;

use KumbiaPHP\Upload\Upload;
use KumbiaPHP\Kernel\Request;

/**
 * Clase para guardar archivo subido
 *
 * @category   Kumbia
 * @package    Upload
 */
class File extends Upload
{

    /**
     * Constructor
     *
     * @param Request $request
     * @param string $name nombre de archivo por metodo POST 
     * 
     */
    public function __construct(Request $request, $name)
    {
        parent::__construct($request, $name);
        // Ruta donde se guardara el archivo
        $this->path = dirname($request->getAppContext()->getAppPath()) . '/public/files/upload/';
    }

}
