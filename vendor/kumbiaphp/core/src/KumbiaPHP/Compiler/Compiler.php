<?php

namespace KumbiaPHP\Compiler;

use KumbiaPHP\Compiler\CompilerInterface;
use KumbiaPHP\Compiler\CompilerException;

class Compiler implements CompilerInterface
{

    protected $filename;
    protected $code;
    protected $config;

    /**
     * Arreglo con las rutas donde se van a buscar las clases.
     * @var array
     */
    private $directories = array();

    function __construct($filename)
    {
        if (!is_dir($dir = dirname($filename))) {
            throw new CompilerException("El directorio \"$dir\" No existe");
        }
        $this->filename = $filename;

        $this->config = parse_ini_file('compiler.ini', TRUE);
        $this->code = "<?php\n";
        var_dump($this->config);
        var_dump('Clases Compiladas:');
    }

    /**
     * registra rutas donde se buscarán clases.
     */
    public function registerDirectories($directories)
    {
        $this->directories = $directories;
    }

    public function add($filename)
    {
        if (!is_dir($dir = dirname($filename))) {
            throw new CompilerException("El directorio \"$dir\" No existe");
        }
        if (!is_writable($filename)) {
            throw new CompilerException("No se puede escribir en el Archvio \"$filename\"");
        }

        $this->code .= PHP_EOL . file_get_contents($filename);
    }

    protected function getContent($className)
    {
        $original = $className;
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        foreach ($this->directories as $folder) {
            if (is_file($file = $folder . DIRECTORY_SEPARATOR . $fileName)) {
                var_dump($original);
                $this->add($file);
                return;
            }
        }
    }

    public function compile()
    {
        foreach ($this->includedClasess() as $class) {
            $this->getContent($class);
        }

        $compiled = str_replace("\n<?php", '', $this->code);

        $compiled = preg_replace('@/\*(.*)\*/@Us', '', $compiled);

        file_put_contents($this->filename, $compiled);
    }

    protected function includedClasess()
    {
        return $this->config['include']['class'];
    }

}