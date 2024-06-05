<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\AMP\Core;

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
        if ($this->moduleManagerDomain->isEnabled('XC-Reviews')) {
            $event->addMutations([
                'modules/XC/Reviews/product.items_list.rating.twig' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['amp.itemsList.product.grid.customer.info', 25, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                        ['amp.itemsList.product.list.customer.info', 35, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-FeaturedProducts')) {
            $event->addMutations([
                'CDev\FeaturedProducts\View\Customer\FeaturedProducts' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['amp.center.bottom', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-Bestsellers')) {
            $event->addMutations([
                'CDev\Bestsellers\View\Bestsellers' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['amp.center.bottom', 400, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-ProductAdvisor')) {
            $event->addMutations([
                'CDev\ProductAdvisor\View\NewArrivals' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['amp.center.bottom', 500, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                    ],
                ],
                'CDev\ProductAdvisor\View\ComingSoon'  => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['amp.center.bottom', 600, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-Sale')) {
            $event->addMutations([
                'CDev\Sale\View\SaleBlock' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['amp.center.bottom', 700, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-NewsletterSubscriptions')) {
            $event->addMutations([
                'QSL\AMP\Module\XC\NewsletterSubscriptions\View\SubscribeBlock' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['amp.layout.main.footer', 50, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER ],
                    ],
                ],
            ]);
        }
    }
}
