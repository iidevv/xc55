<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    protected function getSidebarFirstHiddenTargets()
    {
        return array_merge(
            parent::getSidebarFirstHiddenTargets(),
            [
                'extra_coupons_and_discounts',
            ]
        );
    }

    protected function getSidebarSecondHiddenTargets()
    {
        return array_merge(
            parent::getSidebarSecondHiddenTargets(),
            [
                'extra_coupons_and_discounts',
            ]
        );
    }
}