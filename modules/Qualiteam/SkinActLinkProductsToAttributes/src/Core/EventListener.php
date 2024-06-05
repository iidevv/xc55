<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActLinkProductsToAttributes\Core;

use XCart\Domain\ModuleManagerDomain;
use XCart\Event\Service\ViewListMutationEvent;
use XLite;

final class EventListener
{
    private ModuleManagerDomain $moduleManagerDomain;

    public function __construct(ModuleManagerDomain $moduleManagerDomain)
    {
        $this->moduleManagerDomain = $moduleManagerDomain;
    }

    public function onCollectViewListMutations(ViewListMutationEvent $event): void
    {
        $event->addMutations([
            'shopping_cart/parts/item.remove.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['cart.item', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
        ]);
    }
}