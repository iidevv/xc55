<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\OrdersImport\Core;

use XCart\Event\Service\ViewListMutationEvent;
use XLite;

final class EventListener
{
    public function onCollectViewListMutations(ViewListMutationEvent $event): void
    {
        $event->addMutations([
            'import/parts/begin.files.orders_note.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['import.begin.content.files', XLite::INTERFACE_WEB, XLite::ZONE_ADMIN],
                ],
            ],
        ]);
    }
}
