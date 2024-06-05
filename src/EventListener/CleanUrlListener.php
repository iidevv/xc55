<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

final class CleanUrlListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!in_array($request->getPathInfo(), ['/', '/admin/'], true)) {
            $_GET['url'] = substr($request->getPathInfo(), 1);
        }
    }
}
