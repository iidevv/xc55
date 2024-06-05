<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UpdateInventory\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ImportExport extends \XLite\View\Tabs\ImportExport
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                \XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if ($this->isImportAllowed()) {
            $list[\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY] = [
                'weight' => 300,
                'title'  => static::t('Update inventory'),
                'widget' => 'XC\UpdateInventory\View\UpdateInventory'
            ];
        }

        return $list;
    }
}
