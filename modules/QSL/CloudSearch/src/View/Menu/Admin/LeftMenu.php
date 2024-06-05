<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Left menu widget
 *
 * @Extender\Mixin
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $title = static::t('Search & Filters');

        if (isset($items['store_setup'][static::ITEM_CHILDREN])) {
            $items['store_setup'][static::ITEM_CHILDREN]['cloud_search'] = [
                static::ITEM_TITLE      => $title,
                static::ITEM_TARGET     => 'module',
                static::ITEM_EXTRA      => ['moduleId' => 'QSL-CloudSearch'],
                static::ITEM_WEIGHT     => 350,
            ];
        } elseif (isset($items['catalog'][static::ITEM_CHILDREN])) {
            $items['catalog'][static::ITEM_CHILDREN]['cloud_search'] = [
                static::ITEM_TITLE      => $title,
                static::ITEM_TARGET     => 'module',
                static::ITEM_EXTRA      => ['moduleId' => 'QSL-CloudSearch'],
                static::ITEM_WEIGHT     => 440,
            ];
        }

        return $items;
    }
}
