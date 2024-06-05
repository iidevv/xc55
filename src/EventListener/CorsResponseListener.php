<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class CorsResponseListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (strpos($event->getRequest()->getPathInfo(), '/api/') !== 0) {
            return;
        }

        $headers = $event->getResponse()->headers;
        $headers->set('Access-Control-Allow-Origin', '*');

        if ($event->getRequest()->getMethod() === 'OPTIONS') {
            $headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS, POST, PUT, PATCH, DELETE');
            $headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type, Accept, X-Auth-Token');
            $headers->set('Access-Control-Allow-Credentials', 'true');
            $headers->set('Access-Control-Max-Age', '3600');
            $headers->set('Cache-Control', 'no-cache, must-revalidate');

            $event->getResponse()->setStatusCode(200);
        }
    }
}
