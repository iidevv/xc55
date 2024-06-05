<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\NotFinishedOrders\Core;

use XCart\Event\Service\ViewListMutationEvent;

final class EventListener
{
    public function onCollectViewListMutations(ViewListMutationEvent $event): void
    {
        $event->addMutations([
            'failed_transaction/parts/transaction_url.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['failed_transaction.after'],
                ],
            ],
        ]);
    }
}
