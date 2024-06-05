<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActMagicImages\Core;

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
//        $event->addMutations([
//            'items_list/product/parts/grid.photo.twig'                                      => [
//                ViewListMutationEvent::TO_REMOVE => [
//                    ['itemsList.product.grid.customer.mainBlock', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
//                ],
//            ],
//            'modules/Qualiteam/SkinActMagicImages/items_list/product/parts/grid.photo.twig' => [
//                ViewListMutationEvent::TO_INSERT => [
//                    ['itemsList.product.grid.customer.mainBlock', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
//                ],
//            ],
//        ]);
    }
}