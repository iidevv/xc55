<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use XCart\Domain\StaticConfigDomain;

final class ConfigListener
{
    private StaticConfigDomain $staticConfigDomain;

    public function __construct(StaticConfigDomain $staticConfigDomain)
    {
        $this->staticConfigDomain = $staticConfigDomain;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $config = $this->staticConfigDomain->getConfig();

        if (
            ($domains = $config['host_details']['domains'] ?? [])
            && ($httpHost = $event->getRequest()->server->get('HTTP_HOST'))
        ) {
            foreach (['http_host', 'https_host'] as $host) {
                if (
                    ($config['host_details'][$host] ?? null) !== $httpHost
                    && in_array($httpHost, $domains, true)
                ) {
                    $config['host_details'][$host] = $httpHost;
                }
            }

            $this->staticConfigDomain->setConfig($config);
        }
    }
}
