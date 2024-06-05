<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\SyncData;

use Qualiteam\SkinActQuickbooks\View\Tabs\QuickbooksSyncData;

abstract class ASyncData extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                QuickbooksSyncData::TAB_ORDERS,
                QuickbooksSyncData::TAB_PRODUCTS,
                QuickbooksSyncData::TAB_VARIANTS,
                QuickbooksSyncData::TAB_CUSTOMERS,
                QuickbooksSyncData::TAB_ERRORS,
            ]
        );
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActQuickbooks/sync_data';
    }
}