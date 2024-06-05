<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\SpecialOffersBase\Core;

use XCart\Event\Service\ViewListMutationEvent;
use XLite;

final class EventListener
{
    public function onCollectViewListMutationsAfter(ViewListMutationEvent $event): void
    {
        $event->addMutations([
            'shopping_cart/parts/item.subtotal.twig'    => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['cart.item', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
        ]);
    }
}
