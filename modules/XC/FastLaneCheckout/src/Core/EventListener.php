<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\FastLaneCheckout\Core;

use XCart\Event\Service\ViewListMutationEvent;
use XLite;

final class EventListener
{
    public function onCollectViewListMutations(ViewListMutationEvent $event): void
    {
        $event->addMutations([
            'XLite\View\AllInOneSolutions'                 => [
                ViewListMutationEvent::TO_INSERT => [
                    ['checkout_fastlane.header.top', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                ],
            ],
            'layout/header/header.bar.checkout.logos.twig' => [
                ViewListMutationEvent::TO_INSERT => [
                    ['checkout_fastlane.header.top', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                ],
            ],
        ]);
    }
}
