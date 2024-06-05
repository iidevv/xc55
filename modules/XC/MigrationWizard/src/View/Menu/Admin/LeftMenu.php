<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        $list['migration_wizard'] = [
            static::ITEM_TITLE    => static::t('Migration wizard'),
            static::ITEM_TARGET   => 'migration_wizard',
            static::ITEM_WEIGHT   => 50,
            static::ITEM_ICON_SVG => \XLite\View\AView::MIGRATION_WIZARD_MODULE_PATH . '/menu_icon.svg',
        ];

        return $list;
    }
}
