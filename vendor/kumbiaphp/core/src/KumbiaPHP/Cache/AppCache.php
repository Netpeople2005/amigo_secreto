<?php

namespace KumbiaPHP\Cache;

use KumbiaPHP\Cache\Cache;
use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Kernel\KernelInterface;

/**
 * Description of CacheKernel
 *
 * @author maguirre
 */
class AppCache implements KernelInterface
{

    /**
     *
     * @var Kernel
     */
    protected $kernel;

    /**
     *
     * @var Cache 
     */
    protected $cache;

    function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function execute(Request $request, $type = KernelInterface::MASTER_REQUEST)
    {

//        if (!$this->kernel->isProduction()) {
//            return $this->kernel->execute($request);
//        }

        $this->kernel->init($request);

        $this->cache = $this->kernel->get('cache');

        $id = md5($request->getRequestUrl() . $request->server->get('QUERY_STRING'));

        if ($this->isMethodCacheable($request) &&
                (($response = $this->cache->get($id)) instanceof Response)) {
            if ('text/html' === $response->headers->get('Content-Type', 'text/html')) {
                echo '<!-- Tiempo: ' . round(microtime(1) - START_TIME, 4) . ' seg. -->';
            }
        } else {
            $response = $this->kernel->execute($request);

            if ($this->isCacheable($request, $response)) {
                $this->cache->save($id, $response);
            } else {
                $this->cache->remove($id);
            }
        }

        return $response;
    }

    protected function isCacheable(Request $request, Response $response)
    {
        return $this->isMethodCacheable($request) &&
                in_array($response->getStatusCode(), array(
                    200, 203, 300, 301, 302, 404, 410));
    }

    protected function isMethodCacheable(Request $request)
    {
        return in_array($request->getMethod(), array('GET', 'HEAD'));
    }

    public static function get($service)
    {
        
    }

    public static function getParam($param)
    {
        
    }

}
