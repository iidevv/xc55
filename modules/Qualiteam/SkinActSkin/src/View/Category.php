<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Category widget
 *
 * @Extender\Mixin
 */
class Category extends \XLite\View\Category
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        if (($key = array_search('main', $result)) !== false) {
            unset($result[$key]);
        }

        return $result;
    }

}
