<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\XC\Reviews\View;

use XCart\Extender\Mapping\Extender;

/**
 * Reviews tab on product details page
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\Reviews")
 */
class ReviewsTab extends \XC\Reviews\View\Product\ReviewsTab
{

    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            ['product']
        );
    }

}
