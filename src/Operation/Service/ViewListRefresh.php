<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Operation\Service;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use XCart\Event\Service\ViewListEvent;
use XCart\Operation\Service\ViewList\ViewListMutationApply;
use XCart\Operation\Service\ViewList\ViewListMutationCollect;
use XCart\Operation\Service\ViewList\ViewListOverridesApply;
use XCart\Operation\Service\ViewList\ViewListRead;
use XCart\Operation\Service\ViewList\ViewListSave;

final class ViewListRefresh
{
    private ViewListRead $viewListRead;

    private ViewListSave $viewListSave;

    private ViewListMutationCollect $viewListMutationCollect;

    private ViewListMutationApply $viewListMutationApply;

    private ViewListOverridesApply $viewListOverridesApply;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ViewListRead $viewListRead,
        ViewListSave $viewListSave,
        ViewListMutationCollect $viewListMutationCollect,
        ViewListMutationApply $viewListMutationApply,
        ViewListOverridesApply $viewListOverridesApply,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->viewListRead            = $viewListRead;
        $this->viewListSave            = $viewListSave;
        $this->viewListMutationCollect = $viewListMutationCollect;
        $this->viewListMutationApply   = $viewListMutationApply;
        $this->viewListOverridesApply  = $viewListOverridesApply;
        $this->eventDispatcher         = $eventDispatcher;
    }
    public function __invoke(): void
    {
        $event = new ViewListEvent([]);

        $this->eventDispatcher->dispatch($event, 'xcart.service.refresh-view-lists.before');

        $event->setList(array_merge($event->getList(), ($this->viewListRead)()));

        $versionKey = $this->generateVersionKey();

        ($this->viewListSave)($event->getList(), $versionKey);

        $mutations = ($this->viewListMutationCollect)();

        ($this->viewListMutationApply)($mutations, $versionKey);

        ($this->viewListOverridesApply)($versionKey);

        $this->eventDispatcher->dispatch($event, 'xcart.service.refresh-view-lists.after');
    }

    private function generateVersionKey(): string
    {
        return md5(microtime() . random_int(1, 1000));
    }
}
