<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\EventListener;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use XCart\Container;
use XCart\Kernel;

class ContainerInjectorListener
{
    private Kernel $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function injectContainerToConsole(ConsoleCommandEvent $event)
    {
        Container::setContainer($this->kernel->getContainer());
    }
}
