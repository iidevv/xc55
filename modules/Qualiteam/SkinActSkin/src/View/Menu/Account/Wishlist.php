<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\Menu\Account;

use Qualiteam\SkinActSkin\View\Menu\AccountMenuItem;
use XLite\Core\Converter;
use XLite\Core\Translation\Label;
use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;

/**
 * @Extender\Depend("QSL\MyWishlist")
 *
 * @ListChild (list="layout.header.bar.links.account", weight="450")
 */
class Wishlist extends AccountMenuItem
{
    public function getBadge(): string
    {
        $result = '0';

        if ($wishlist = \QSL\MyWishlist\Core\Wishlist::getInstance()->getWishlist()) {
            $result = (string)$wishlist->getProductsCount();
        }

        return $result;
    }

    /**
     * @return string|Label
     */
    public function getTitle()
    {
        return static::t('Wishlist');
    }

    public function getURL(): string
    {
        return Converter::buildURL('wishlist');
    }

    public function getIcon(): string
    {
        return 'i-heart';
    }

    public function getCSSClass(): string
    {
        return parent::getCSSClass() . ' account-links__list-item--wishlist wishlist-label';
    }

    public function getBadgeCSSClass(): string
    {
        return parent::getBadgeCSSClass() . ' wishlist-product-count';
    }

    public function getJSFiles(): array
    {
        return array_merge(
            parent::getJSFiles(),
            ['modules/QSL/MyWishlist/js/account-menu.js']
        );
    }
}
