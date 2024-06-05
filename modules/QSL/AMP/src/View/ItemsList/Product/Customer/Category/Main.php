<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\ItemsList\Product\Customer\Category;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Main extends \XLite\View\ItemsList\Product\Customer\Category\Main
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $targets = parent::getAllowedTargets();

        if (static::isAMP() && !in_array('category', $targets)) {
            $targets[] = 'category';
        }

        return $targets;
    }
}
