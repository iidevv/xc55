<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\LoyaltyProgram\Core;

use XCart\Event\Service\ViewListMutationEvent;

final class EventListener
{
    public function onCollectViewListMutations(ViewListMutationEvent $event): void
    {
        $event->addMutations([
            // By default, the "Totals" line has the weight of 30. Since VAT module
            // adds its own line with the same weight, there is no way to insert
            // "You will earn X points" line between them. To do this we have to
            // move the "Totals" line lower by assigning it the weight of 34.
            'shopping_cart/parts/total.total.twig' => [
                ['cart.panel.totals', 'customer'],
                ['cart.panel.totals', '34', 'customer'],
            ],
        ]);
    }
}
