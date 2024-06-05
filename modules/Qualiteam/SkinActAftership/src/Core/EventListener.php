<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActAftership\Core;

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
        $event->addMutations([
            'items_list/order/parts/shipping.name.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['orders.children.shipping', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'checkout/steps/shipping/parts/shippingMethods.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['checkout.shipping.selected.sub.payment', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'order_tracking_information/parts/tracking_info.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['tracking.info', XLite::INTERFACE_MAIL, XLite::ZONE_COMMON],
                ],
            ],
        ]);
    }
}
