<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View\ItemsList\Model\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\View\ItemsList\Model\Customer\Review
{
    protected function isVisible()
    {
        return false;
    }
}