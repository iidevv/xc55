<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCreateOrder\Core;

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
            'order/page/parts/placed.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['order.operations', XLite::INTERFACE_WEB, XLite::ZONE_ADMIN ],
                ],
            ],
            'modules/Qualiteam/SkinActCreateOrder/order/page/parts/placed.twig' => [
                ViewListMutationEvent::TO_INSERT => [
                    ['order.operations', 30, XLite::INTERFACE_WEB, XLite::ZONE_ADMIN ],
                ],
            ]
        ]);
    }
}