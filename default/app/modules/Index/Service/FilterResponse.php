<?php

namespace Index\Service;

use KumbiaPHP\Kernel\Event\ResponseEvent;

/**
 * La clase Ejecuta funcionalidades en el evento kumbia.response
 *
 * @author manuel
 */
class FilterResponse
{

    public function onResponse(ResponseEvent $event)
    {
        if ($event->getRequest()->isAjax() &&
                ($event->getResponse()->headers->has('Location'))) {
            //cuando la petición sea ajax y estemos redirigiendo, lo hacemos por javascript.
            $url = $event->getResponse()->headers->get('Location');
            $script = "<script>window.location='$url';</script>";
            $event->getResponse()->setContent($script);
            $event->getResponse()->headers->delete('Location');
            $event->stopPropagation();
        }
    }

}