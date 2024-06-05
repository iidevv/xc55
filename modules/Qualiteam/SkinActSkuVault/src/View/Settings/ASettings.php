<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\Settings;

use Qualiteam\SkinActSkuVault\View\Tabs\SkuVault;

abstract class ASettings extends \XLite\View\AView
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
                SkuVault::TAB_GENERAL,
                SkuVault::TAB_PRODUCTS,
                SkuVault::TAB_ORDERS,
                SkuVault::TAB_STATUSES_MAPPING,
                SkuVault::TAB_LOGS,
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
        return 'modules/Qualiteam/SkinActSkuVault/settings';
    }
}
