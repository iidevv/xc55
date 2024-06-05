<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use Symfony\Component\HttpKernel\Event\TerminateEvent;

final class RunPostRequestActionsListener
{
    public function onKernelTerminate(TerminateEvent $event): void
    {
        $xc = \XLite::getInstance();
        $xc->runPostRequestActions();
    }
}
