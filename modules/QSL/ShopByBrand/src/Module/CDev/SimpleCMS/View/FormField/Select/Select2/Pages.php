<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Module\CDev\SimpleCMS\View\FormField\Select\Select2;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use QSL\ShopByBrand\Model\Menu;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Pages extends \CDev\SimpleCMS\View\FormField\Select\Select2\Pages
{
    /**
     * @return array
     */
    public static function getAllPages()
    {
        return array_merge(
            parent::getAllPages(),
            static::getBrandsPages()
        );
    }

    /**
     * @return array
     */
    protected static function defineBrandsPage()
    {
        return [
            Menu::DEFAULT_BRANDS_PAGE => static::t('Brands')
        ];
    }

    /**
     * @return array
     */
    protected static function defineBrandsPages()
    {
        $list   = [];
        $brands = Database::getRepo('QSL\ShopByBrand\Model\Brand')->findAll();

        /** @var \QSL\ShopByBrand\Model\Brand $brand */
        foreach ($brands as $brand) {
            $list['?target=brand&brand_id=' . $brand->getId()] = $brand->getName();
        }

        return $list;
    }

    /**
     * @return array
     */
    protected static function getBrandsPages()
    {
        return array_merge(
            static::defineBrandsPage(),
            static::addPrefixToPagesList(
                static::t('Brands'),
                static::defineBrandsPages()
            )
        );
    }
}
