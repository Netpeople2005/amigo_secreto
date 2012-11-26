<?php

namespace KumbiaPHP\Kernel\Session;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Session\SessionInterface;

/**
 * Description of Session
 *
 * @author manuel
 */
class Session implements SessionInterface
{

    protected $namespaceApp;

    public function __construct($appPath)
    {
        $this->namespaceApp = md5($appPath);
        $this->start();
    }

    public function start()
    {
        session_name($this->namespaceApp);
        session_start();
    }

    public function destroy()
    {
        session_unset();
        session_destroy();
    }

    public function get($key, $namespace = 'default')
    {
        return $this->has($key, $namespace) ? $_SESSION[$this->namespaceApp][$namespace][$key] : NULL;
    }

    public function has($key, $namespace = 'default')
    {
        return isset($_SESSION[$this->namespaceApp]) &&
                isset($_SESSION[$this->namespaceApp][$namespace]) &&
                array_key_exists($key, $_SESSION[$this->namespaceApp][$namespace]);
    }

    public function set($key, $value, $namespace = 'default')
    {
        $_SESSION[$this->namespaceApp][$namespace][$key] = $value;
    }

    public function delete($key = null, $namespace = 'default')
    {
        if ($key !== null) {
            if ($this->has($key, $namespace)) {
                unset($_SESSION[$this->namespaceApp][$namespace][$key]);
            }
        } elseif (isset($_SESSION[$this->namespaceApp]) &&
                isset($_SESSION[$this->namespaceApp][$namespace])) {
            unset($_SESSION[$this->namespaceApp][$namespace]);
        }
    }

    public function all($namespace = null)
    {
        if (null !== $namespace) {
            return isset($_SESSION[$this->namespaceApp]) &&
                    isset($_SESSION[$this->namespaceApp][$namespace]) ?
                    $_SESSION[$this->namespaceApp][$namespace] : array();
        } else {
            return isset($_SESSION[$this->namespaceApp]) ?
                    $_SESSION[$this->namespaceApp] : array();
        }
    }

}