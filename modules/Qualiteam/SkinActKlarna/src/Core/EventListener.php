<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActKlarna\Core;

use XCart\Domain\ModuleManagerDomain;
use XCart\Event\Service\ViewListMutationEvent;

final class EventListener
{
    private ModuleManagerDomain $moduleManagerDomain;

    public function __construct(
        ModuleManagerDomain $moduleManagerDomain
    ) {
        $this->moduleManagerDomain = $moduleManagerDomain;

    }

    public function onCollectViewListMutations(ViewListMutationEvent $event): void
    {
        // TODO: add moveTemplatesInLists here
    }
}