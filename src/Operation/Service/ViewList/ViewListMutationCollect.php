<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Operation\Service\ViewList;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use XCart\Event\Service\ViewListMutationEvent;

final class ViewListMutationCollect
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(): array
    {
        $event = new ViewListMutationEvent();

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-list.collect-mutation.before');

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-list.collect-mutation');

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-list.collect-mutation.after');

        return $event->getMutations();
    }
}
