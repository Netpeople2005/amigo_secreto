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
 * @package    Cache
 * @subpackage Drivers 
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

namespace KumbiaPHP\Cache\Adapter;

use KumbiaPHP\Cache\Cache;
use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;

/**
 * Cacheo de Archivos
 *
 * @category   Kumbia
 * @package    Cache
 * @subpackage Drivers
 */
class File extends Cache
{

    /**
     * Obtiene el nombre de archivo a partir de un id y grupo
     *
     * @param string $id
     * @param string $group
     * @return string
     */
    protected function getFilename($id, $group)
    {
        return $this->appPath . 'temp/cache/cache_' . md5($id) . '.' . md5($group);
    }

    public function get($id, $group = 'default')
    {
        $filename = $this->getFilename($id, $group);
        if (is_file($filename)) {
            $fh = fopen($filename, 'r');

            $lifetime = trim(fgets($fh));
            if ($lifetime == 'undefined' || $lifetime >= time()) {
                $data = stream_get_contents($fh);
                $response = unserialize($data);
            } else {
                $response = null;
            }

            fclose($fh);
            return $response;
        }
        return null;
    }

    public function getContent($id, $group = 'default')
    {
        $filename = $this->getFilename($id, $group);
        if (is_file($filename)) {
            $fh = fopen($filename, 'r');

            $lifetime = trim(fgets($fh));
            if ($lifetime == 'undefined' || $lifetime >= time()) {
                $response = stream_get_contents($fh);
            } else {
                $response = null;
            }

            fclose($fh);
            return $response;
        }
        return null;
    }

    public function save($id, $response)
    {

        $cacheInfo = $response->getCacheInfo();

        $time = isset($cacheInfo['time']) ? strtotime($cacheInfo['time']) : 0;
        $group = isset($cacheInfo['group']) ? $cacheInfo['group'] : 'default';

        if (!$time) {
            return;
        }

        $content = $time . PHP_EOL . serialize($response);


        return file_put_contents($this->getFilename($id, $group), $content);
    }

    public function saveContent($id, $value, $time = NULL, $group = 'default')
    {
        if (!$time) {
            return;
        }

        $content = strtotime($time) . PHP_EOL . $value;


        return file_put_contents($this->getFilename($id, $group), $content);
    }

    public function clean($group = false)
    {
        $pattern = $group ? $this->appPath . 'temp/cache/' . '*.' . md5($group) : $this->appPath . 'temp/cache/*';
        foreach (glob($pattern) as $filename) {
            if (!unlink($filename)) {
                return false;
            }
        }
        return true;
    }

    public function remove($id, $group = 'default')
    {
        if (is_file($file = $this->getFilename($id, $group))) {
            return unlink($file);
        }
    }

}