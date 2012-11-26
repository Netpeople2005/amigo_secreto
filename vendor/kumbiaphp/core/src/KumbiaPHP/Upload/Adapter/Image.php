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

use KumbiaPHP\Upload\Exception\UploadException;
use KumbiaPHP\Kernel\Request;

/**
 * Clase para guardar imagen subida
 *
 * @category   Kumbia
 * @package    Upload
 */
class Image extends Upload
{

    /**
     * Ancho mínimo de la imagen
     * 
     * @var int
     */
    protected $minWidth = NULL;

    /**
     * Ancho máximo de la imagen
     *
     * @var int
     */
    protected $maxWidth = NULL;

    /**
     * Alto mínimo de la imagen
     * 
     * @var int
     */
    protected $minHeight = NULL;

    /**
     * Alto máximo de la imagen
     *
     * @var int
     */
    protected $maxHeight = NULL;

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
        $this->path = dirname($request->getAppContext()->getAppPath()) . '/public/img/upload/';
    }

    /**
     * Asigna el ancho mínimo de la imagen
     * 
     * @param int $value
     */
    public function setMinWidth($value)
    {
        $this->minWidth = $value;
    }

    /**
     * Asigna el ancho máximo de la imagen
     * 
     * @param int $value
     */
    public function setMaxWidth($value)
    {
        $this->maxWidth = $value;
    }

    /**
     * Asigna el alto mínimo de la imagen
     * 
     * @param int $value
     */
    public function setMinHeight($value)
    {
        $this->minHeight = $value;
    }

    /**
     * Asigna el alto máximo de la imagen
     * 
     * @param int $value
     */
    public function setMaxHeight($value)
    {
        $this->maxHeight = $value;
    }

    /**
     * Valida el archivo antes de guardar
     * 
     * @return boolean
     */
    protected function validates()
    {
        // Verifica que se pueda escribir en el directorio
        if (!is_writable($this->path)) {
            $this->errors[] = 'Error: no se puede escribir en el directorio';
            return FALSE;
        }

        // Verifica que sea un archivo de imagen
        if (!preg_match('/^image\//i', $this->file->getType())) {
            $this->errors[] = 'Error: el archivo debe ser una imagen';
            return FALSE;
        }

        // Verifica ancho minimo de la imagen
        if ($this->minWidth !== NULL) {
            // Obtiene datos de la imagen
            $imageSize = getimagesize($this->file->getTmpName());

            if ($imageSize[0] < $this->minWidth) {
                $this->errors[] = "Error: el ancho de la imagen debe ser superior o igual a {$this->minWidth}px";
                return FALSE;
            }
        }

        // Verifica ancho maximo de la imagen
        if ($this->maxWidth !== NULL) {
            if (!isset($imageSize)) {
                // Obtiene datos de la imagen
                $imageSize = getimagesize($this->file->getTmpName());
            }

            if ($imageSize[0] > $this->maxWidth) {
                $this->errors[] = "Error: el ancho de la imagen debe ser inferior o igual a {$this->maxWidth}px";
                return FALSE;
            }
        }

        // Verifica alto minimo de la imagen
        if ($this->minHeight !== NULL) {
            // Obtiene datos de la imagen
            $imageSize = getimagesize($this->file->getTmpName());

            if ($imageSize[1] < $this->minHeight) {
                $this->errors[] = "Error: el alto de la imagen debe ser superior o igual a {$this->minHeight}px";
                return FALSE;
            }
        }

        // Verifica alto maximo de la imagen
        if ($this->maxHeight !== NULL) {
            if (!isset($imageSize)) {
                // Obtiene datos de la imagen
                $imageSize = getimagesize($this->file->getTmpName());
            }

            if ($imageSize[1] > $this->maxHeight) {
                $this->errors[] = "Error: el alto de la imagen debe ser inferior o igual a {$this->maxHeight}px";
                return FALSE;
            }
        }

        // Validaciones
        return parent::validates();
    }

    /**
     * Valida que el tipo de archivo
     *
     * @return boolean
     */
    protected function validatesTypes()
    {
        foreach ($this->_types as $type) {
            if ($this->file->getType() == "image/$type") {
                return TRUE;
            }
        }

        return FALSE;
    }

}
