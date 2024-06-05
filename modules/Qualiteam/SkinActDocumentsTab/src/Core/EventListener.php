<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActDocumentsTab\Core;

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
        if ($this->moduleManagerDomain->isEnabled('CDev-FileAttachments')) {
            $event->addMutation('modules/CDev/FileAttachments/file_attachments.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.tab.description', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER]
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.documents', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER]
                ]
            ]);
        }
    }
}
