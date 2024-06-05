<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\Core;

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
            'modules/XC/Reviews/review/parts/field.review.twig'   => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['review.add.fields', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'modules/XC/Reviews/review/parts/field.email.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['review.add.fields', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
        ]);
    }
}
