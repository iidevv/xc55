<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use XLite\Core\Database;

final class SessionListener
{
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || $event->getRequest()->getMethod() === 'OPTIONS') {
            return;
        }

        $session = $this->container && $this->container->has('initialized_session')
            ? $this->container->get('initialized_session')
            : $event->getRequest()->getSession();

        if (
            $session
            && $session->isStarted()
            && $session->has('profile_id')
            && $profile = Database::getRepo('XLite\Model\Profile')->find($session->get('profile_id'))
        ) {
            $session->set('salt', $profile->getSalt());
        }
    }
}
