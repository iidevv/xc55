<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\Core;

use XCart\Domain\ModuleManagerDomain;
use XCart\Event\Service\ViewListMutationEvent;
use XLite;
use XLite\Core\Config;

final class EventListener
{
    private ModuleManagerDomain $moduleManagerDomain;

    public function __construct(
        ModuleManagerDomain $moduleManagerDomain
    ) {
        $this->moduleManagerDomain = $moduleManagerDomain;
    }

    /**
     * @param ViewListMutationEvent $event
     */
    public function onCollectViewListMutations(ViewListMutationEvent $event): void
    {
        $event->addMutations([
            'layout/header/header.right.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.bar', 500, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.links.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.bar', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.right', 15, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.links.logged.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.bar', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.right', 15, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/footer/main.footer.contacts.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.main.footer', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/logo.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.mobile.menu', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.product-price.twig'            => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.info.bottom', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.right.mobile.minicart.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.right.mobile', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.mobile.menu.right', 30, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.briefDescription.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.price.twig'            => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.info', 11, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', 11, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
//            'product/details/parts/page.tabs.attributes.twig' => [
//                ViewListMutationEvent::TO_REMOVE => [
//                    ['product.details.page.tab.attributes', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
//                ],
//                ViewListMutationEvent::TO_INSERT => [
//                    ['product.details.page.info.attributes-box', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
//                ],
//            ],
            'XLite\View\PoweredBy' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['sidebar.footer', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['sidebar.footer', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'XLite\View\Category' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['center', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['category-block.info', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'XLite\View\TopCategories' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['sidebar.first', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],

            ],
            'XLite\View\Subcategories' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['center.bottom', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.main.center.top', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
        ]);

        if (
            $this->moduleManagerDomain->isEnabled('XC-ProductComparison')
            || $this->moduleManagerDomain->isEnabled('QSL-MyWishlist')
            || Config::getInstance()->General->enable_add2cart_button_grid
        ) {
            $event->addMutation('items_list/product/parts/grid.buttons-container.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.mainBlock', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.info.bottom', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('QSL-MyWishlist')) {
            $event->addMutations([
                'modules/QSL/MyWishlist/header/header.bar.links.logged.wishlist.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['layout.header.bar.links.logged', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['layout.header.bar.links.list', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'modules/QSL/MyWishlist/header/header.bar.wishlist.twig'=> [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['layout.header.right', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
            $event->addMutation('QSL\MyWishlist\View\AddButtonItemsList', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.buttons', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.info', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-Reviews')) {
            $event->addMutations([
                'modules/XC/Reviews/product.items_list.rating.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['itemsList.product.grid.customer.info.top', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/product_details.rating.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['product.details.page.info', 15, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'XC\Reviews\View\Product\ReviewsTab' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.details.page.tab.reviews', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['center.bottom', 720, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/reviews_tab/parts/tab.all_reviews_link.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.reviews.tab', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['product.reviews.tab.header', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-ProductAdvisor')) {
            $event->addMutations([
                'CDev\ProductAdvisor\View\RecentlyViewed' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['center.bottom', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['layout.main.center.bottom', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'CDev\ProductAdvisor\View\NewArrivals' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['sidebar.second', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-Sale')) {
            $event->addMutations([
                'CDev\Sale\View\SaleBlock' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['sidebar.first', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-Paypal')) {
            $event->addMutations([
                'CDev\Paypal\View\PaypalBadge' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['layout.header.right', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['layout.header.right.mobile', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('QSL-LoyaltyProgram')) {
            $event->addMutation('modules/QSL/LoyaltyProgram/product/parts/product_points.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.plain_price', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.info', 12, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', 12, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }
    }
}
