<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\Menu\Account;

use XLite\Core\Converter;
use Qualiteam\SkinActSkin\View\Menu\AccountMenuItem;
use XC\ProductComparison\Core\Data;
use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Translation\Label;

/**
 * @Extender\Depend("XC\ProductComparison")
 *
 * @ListChild (list="layout.header.bar.links.account", weight="400")
 */
class Compare extends AccountMenuItem
{
    public function isEnabled(): bool
    {
        return (Data::getInstance()->getProductsCount() > 1);
    }

    public function getBadge(): string
    {
        $productsCount = Data::getInstance()->getProductsCount();

        return $productsCount > 0 ? strval($productsCount) : '';
    }

    /**
     * @return string|Label
     */
    public function getTitle()
    {
        return static::t('Compare list');
    }

    public function getURL(): string
    {
        return Converter::buildURL('compare');
    }

    public function getIcon(): string
    {
        return 'i-chart';
    }

    public function getCSSClass(): string
    {
        return parent::getCSSClass() . ' account-links__list-item--compare account-link-compare compare-indicator';
    }

    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            ['modules/XC/ProductComparison/js/account-menu.js']
        );
    }
}
