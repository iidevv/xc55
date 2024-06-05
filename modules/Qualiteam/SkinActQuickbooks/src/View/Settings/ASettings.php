<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\Settings;

use Qualiteam\SkinActQuickbooks\View\Tabs\Quickbooks;

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
                Quickbooks::TAB_GENERAL,
                Quickbooks::TAB_QWC_FILES,
                Quickbooks::TAB_SETTINGS,
                Quickbooks::TAB_ORDER_STATUSES,
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
        return 'modules/Qualiteam/SkinActQuickbooks/settings';
    }
}