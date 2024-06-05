<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Volume discount
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class VolumeDiscount extends \XLite\View\Dialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'volume_discount';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/VolumeDiscounts/discount';
    }
}
