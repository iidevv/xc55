<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core;

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
        if ($this->moduleManagerDomain->isEnabled('XC-Reviews')) {
            $event->addMutations([
                'modules/XC/Reviews/reviews_page/parts/page.reviews_list_header.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.reviews.page', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],

                'modules/XC/Reviews/reviews_page/parts/page.reviews_list.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.reviews.page', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],

                'modules/XC/Reviews/reviews_tab/parts/tab.reviews_list_header.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.reviews.tab', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],

                'modules/XC/Reviews/reviews_tab/parts/reviews-header.title.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.reviews.tab.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }
    }
}