<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Module\XC\ProductComparison\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductComparison")
 */
class ProductComparison extends \XC\ProductComparison\View\ProductComparison
{
    public static function getDisallowedTargets()
    {
        return array_merge(
            parent::getDisallowedTargets(),
            [
                'page'
            ]
        );
    }
}
