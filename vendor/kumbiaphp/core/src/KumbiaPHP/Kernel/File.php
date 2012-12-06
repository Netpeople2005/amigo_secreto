<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Exception\FileException;

/**
 * Description of File
 *
 * @author manuel
 */
class File
{

    protected $name;
    protected $extension;
    protected $tmpName;
    protected $type;
    protected $size;
    protected $error;

    public function __construct(array $data = array())
    {
        isset($data['name']) && $this->name = $data['name'];
        isset($data['tmp_name']) && $this->tmpName = $data['tmp_name'];
        isset($data['type']) && $this->type = $data['type'];
        isset($data['size']) && $this->size = $data['size'];
        isset($data['error']) && $this->error = $data['error'];

        if (1 < count($ext = explode('.', $this->name))) {
            $this->extension = end($ext);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getTmpName()
    {
        return $this->tmpName;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function hasError()
    {
        return 0 < $this->error;
    }

    public function getError()
    {
        if ($this->hasError()) {
            $error = array(
                UPLOAD_ERR_INI_SIZE => 'el archivo excede el tamaño máximo (' . ini_get('upload_max_filesize') . 'b) permitido por el servidor',
                UPLOAD_ERR_FORM_SIZE => 'el archivo excede el tamaño máximo permitido',
                UPLOAD_ERR_PARTIAL => 'se ha subido el archivo parcialmente',
                UPLOAD_ERR_NO_FILE => 'no se ha subido ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'no se encuentra el directorio de archivos temporales',
                UPLOAD_ERR_CANT_WRITE => 'falló al escribir el archivo en disco',
                UPLOAD_ERR_EXTENSION => 'una extensión de php ha detenido la subida del archivo'
            );
            return $error[$this->error];
        } else {
            return null;
        }
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function move($dir, $name = null, $preserveExtension = true)
    {
        if (!is_dir($dir) && false === @mkdir($dir, 0777, true)) {
            throw new FileException("'No existe el directorio $dir");
        }
        if (!is_writable($dir)) {
            throw new FileException("'No existe el directorio $dir");
        }

        if ($name && $preserveExtension) {
            $name .= '.' . $this->getExtension();
        } else {
            $name = $this->name;
        }

        return move_uploaded_file($this->tmpName, rtrim($dir, '/') . '/' . $name);
    }

}