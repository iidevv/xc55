<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActImagesForColors\Core;

use XCart\Domain\ModuleManagerDomain;
use XCart\Event\Service\ViewListMutationEvent;
use XLite;

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

    }
}
