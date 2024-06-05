<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\CrispWhiteSkin\Core;

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

    public function onCollectViewListMutations(ViewListMutationEvent $event): void
    {
        $event->addMutations([
            'authorization/parts/link.forgot.twig'                          => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['customer.signin.popup.links', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'authorization/parts/field.links.twig'                          => [
                ViewListMutationEvent::TO_INSERT => [
                    ['customer.signin.popup.fields', 500, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/content/main.location.twig'                             => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.main', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/content/product.location.twig'                          => [
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.info', 5, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.twig'                                 => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header', 200, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.links.logged.account.twig'            => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.bar.links.logged', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.bar.links.logged', -200, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.links.logged.logout.twig'             => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.bar.links.logged', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.bar.links.logged', 500, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/top_menu.twig'                                   => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.main', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.right.twig'                               => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header', 350, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.search.twig'                          => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.bar', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.bar', 50, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.checkout.logos.twig'                  => [
                ViewListMutationEvent::TO_REMOVE => [],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.right.mobile', 1100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/account_menu.twig'           => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.mobile.menu', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/language_menu.twig'          => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.mobile.menu', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/search_menu.twig'            => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.mobile.menu', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/slidebar_menu.twig'          => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.mobile.menu', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.header.mobile.menu', 2000, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'mini_cart/horizontal/parts/mobile.icon.twig'                   => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['minicart.horizontal.children', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.labels.twig'                   => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.small_thumbnails.customer.details', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.small_thumbnails.customer.info.photo', 30, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.image', 17, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.grid.customer.mainBlock.photo', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'shopping_cart/parts/item.remove.twig'                          => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['cart.item', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['cart.item', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'shopping_cart/parts/item.info.weight.twig'                     => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['cart.item.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['cart.item.info', 15, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/stock/label.twig'                              => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.info', 50, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.info', 50, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.sort-options.twig'             => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.table.customer.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.header', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.header', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.table.customer.header', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.display-modes.twig'            => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.table.customer.header', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.header', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.header', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.table.customer.header', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.product-name.twig'             => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.list.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.info', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.product-price.twig'            => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.info', 50, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/out-of-stock.label.twig'              => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.info', 75, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/center/list/parts/common.product-name.twig' => [
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.list.customer.info', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.field-select-product.twig'     => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.table.customer.columns', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.field-product-qty.twig'        => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.table.customer.columns', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/table.captions.field-select-all.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.table.customer.captions', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/grid.photo.twig'                      => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.mainBlock', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.product-thumbnail.twig'        => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info.photo', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.mainBlock.photo', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.added-mark.twig'               => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.photo', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.small_thumbnails.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.big_thumbnails.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.marks', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.marks', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.table.customer.marks', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.image-next.twig'                  => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.image.photo', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.image-previous.twig'              => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.image.photo', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.loupe.twig'                       => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.image', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.image.photo', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.briefDescription.twig'            => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.info', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.product-editable-attributes.twig' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.info', 35, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', 35, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.more-info-link.twig'              => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.quicklook.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.quicklook.image', 30, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/grid.button-add2cart-wrapper.twig'    => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.tail', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.buttons', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.product-added.twig'               => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.info.form.buttons-added', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info.form.buttons-added', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.product-title.twig'               => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.quicklook.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'checkout/steps/shipping/parts/address.billing.same.twig'       => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['checkout.payment.address.after', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['checkout.payment.address.before', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],

            'XLite\View\MinicartAttributeValues'               => [
                ['minicart.horizontal.item', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ['minicart.horizontal.item.name', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
            ],
            'XLite\View\Product\Details\Customer\PhotoBox'     => [
                ['product.details.page.image', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ['product.details.page.image', 5, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
            ],
            'XLite\View\LanguageSelector\Customer'             => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.bar.links.newby', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['layout.header.bar.links.logged', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'XLite\View\TopContinueShopping'                   => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.main.breadcrumb', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'XLite\View\ShippingEstimator\ShippingEstimateBox' => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['cart.panel.box', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'XLite\View\BannerRotation\BannerRotation'         => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['center', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.main', 350, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
            'XLite\View\Product\Details\Customer\Gallery'      => [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.image', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.image', 11, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.image', 11, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ],
        ]);

        if ($this->moduleManagerDomain->isEnabled('CDev-GoSocial')) {
            $event->addMutations([
                'modules/CDev/GoSocial/product/details/parts/common.share.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['product.details.page.image', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('QSL-ProductStickers')) {
            $event->addMutations([
                'modules/QSL/ProductStickers/items_list/product/parts/common.product_label.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.list.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.table.customer.columns', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['itemsList.product.grid.customer.mainBlock.photo', 75, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.list.customer.photo', 1000, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('QSL-Backorder')) {
            $event->addMutations([
                'modules/QSL/Backorder/product/details/stock/label.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.list.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['itemsList.product.grid.customer.info', 75, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.list.customer.info', 60, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('QSL-ProductQuestions')) {
            $event->addMutations([
                'modules/QSL/ProductQuestions/product/parts/number_of_questions_placeholder.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['product.details.page.info', 23, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-ProductAdvisor')) {
            $event->addMutations([
                'modules/CDev/ProductAdvisor/product/details/parts/common.coming_soon.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['product.details.quicklook.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-FastLaneCheckout')) {
            $event->addMutations([
                'modules/XC/FastLaneCheckout/checkout_fastlane/header/back_button.twig' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['checkout_fastlane.header.left', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-Reviews')) {
            $event->addMutations([
                'modules/XC/Reviews/product.items_list.rating.twig'              => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.list.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['itemsList.product.grid.customer.info', 30, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.list.customer.info', 35, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/product_details.rating.twig'                 => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['product.details.quicklook.info', 25, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['product.details.page.info', 25, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/reviews_page/parts/average_rating.form.twig' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['reviews.page.rating', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/average_rating/form.twig'                    => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['reviews.page.rating', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-SocialLogin')) {
            $event->addMutation('modules/CDev/SocialLogin/signin/signin.checkout.social.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['customer.checkout.signin', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['signin.main', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('QSL-ShopByBrand')) {
            $event->addMutation('modules/QSL/ShopByBrand/product/parts/brand.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.page.info', 30, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', 30, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('Amazon-PayWithAmazon')) {
            $event->addMutation('modules/Amazon/PayWithAmazon/login/signin/signin.checkout.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['customer.checkout.signin', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['signin.main', 30, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-Paypal')) {
            $event->addMutation('modules/CDev/Paypal/login/signin/signin.checkout.paypal.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['customer.checkout.signin', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['signin.main', 40, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-ShopperApproved')) {
            $event->addMutation('modules/XC/ShopperApproved/average_rating/details.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.quicklook.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.details.quicklook.image', 29, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('QSL-LoyaltyProgram')) {
            $event->addMutation('modules/QSL/LoyaltyProgram/product/parts/product_points.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['product.details.page.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['product.details.quicklook.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['product.plain_price', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
            $event->addMutation('modules/QSL/LoyaltyProgram/items_list/product/parts/product_points.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.list.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
            $event->addMutation('QSL\LoyaltyProgram\View\RedeemPoints', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['checkout.review.selected', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['checkout_fastlane.sections.details', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['checkout.review.selected.after', 90, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['checkout_fastlane.sections.details.after', 90, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if (
            $this->moduleManagerDomain->isEnabled('XC-ProductComparison')
            || $this->moduleManagerDomain->isEnabled('QSL-MyWishlist')
            || Config::getInstance()->General->enable_add2cart_button_grid
        ) {
            $event->addMutation('items_list/product/parts/grid.buttons-container.twig', [
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.mainBlock', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('QSL-MyWishlist')) {
            $event->addMutation('modules/QSL/MyWishlist/items_list/product/parts/common.close-button.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.quicklook', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.table.customer.columns', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.buttons', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
            $event->addMutation('modules/QSL/MyWishlist/header/header.bar.wishlist.twig', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.right.mobile', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
            $event->addMutation('modules/QSL/MyWishlist/header/header.bar.links.logged.wishlist.twig', [
                ViewListMutationEvent::TO_INSERT => [
                    ['slidebar.navbar.account.first-additional-menu', 200, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);

            $event->addMutation('QSL\MyWishlist\View\AddButtonItemsList', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.table.customer.columns', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['itemsList.product.grid.customer.buttons', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.list.customer.info', 1250, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['itemsList.product.table.customer.columns', 48, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('CDev-Coupons')) {
            $event->addMutation('CDev\Coupons\View\CartCoupons', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['checkout.review.selected', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['checkout_fastlane.sections.details', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['checkout.review.selected.after', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ['checkout_fastlane.sections.details.after', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-GiftWrapping')) {
            $event->addMutation('XC\GiftWrapping\View\GiftWrapping', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['checkout.review.selected', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['checkout.review.selected', 300, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-Geolocation')) {
            $event->addMutation('XC\Geolocation\View\Button\LocationSelectPopup', [
                ['layout.header.bar', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-MultiCurrency')) {
            $event->addMutation('XC\MultiCurrency\View\LanguageSelector\CustomerMobile', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.header.mobile.menu', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['slidebar.settings', 0, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-NewsletterSubscriptions')) {
            $event->addMutation('XC\NewsletterSubscriptions\View\SubscribeBlock', [
                ViewListMutationEvent::TO_REMOVE => [
                    ['layout.main.footer', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
                ViewListMutationEvent::TO_INSERT => [
                    ['layout.main.footer.before', 10, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                ],
            ]);
        }

        if ($this->moduleManagerDomain->isEnabled('XC-ProductComparison')) {
            $event->addMutations([
                'XC\ProductComparison\View\AddToCompare\Product' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['product.details.page.info.form.buttons.cart-buttons', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['product.details.page.info.form.buttons-added.cart-buttons', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['product.details.quicklook.info.form.buttons.cart-buttons', 120, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['product.details.quicklook.info.form.buttons-added.cart-buttons', 129, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['product.details.page.info.form.buttons.cart-buttons', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['product.details.page.info.form.buttons-added.cart-buttons', 100, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],

                'XC\ProductComparison\View\AddToCompare\ProductCompareIndicator' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['layout.header.right', 50, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['layout.header.right.mobile', 50, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],

                'XC\ProductComparison\View\AddToCompare\ProductCompareLink' => [
                    ViewListMutationEvent::TO_INSERT => [
                        ['slidebar.additional-menu.links', 20, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],

                'XC\ProductComparison\View\AddToCompare\Products' => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['itemsList.product.grid.customer.info', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                    ViewListMutationEvent::TO_INSERT => [
                        ['itemsList.product.grid.customer.buttons', 200, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.list.customer.info', 1200, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['itemsList.product.table.customer.columns', 47, XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
                'XC\ProductComparison\View\ProductComparison'     => [
                    ViewListMutationEvent::TO_REMOVE => [
                        ['sidebar.single', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                        ['sidebar.second', XLite::INTERFACE_WEB, XLite::ZONE_CUSTOMER],
                    ],
                ],
            ]);
        }
    }
}
