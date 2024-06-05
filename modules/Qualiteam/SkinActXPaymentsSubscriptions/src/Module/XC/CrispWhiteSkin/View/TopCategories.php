<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Module\XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * TopCategories decorator
 *
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActXPaymentsSubscriptions","XC\CrispWhiteSkin"})
 */
abstract class TopCategories extends \XLite\View\TopCategories
{
    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return array_merge(parent::getDisallowedTargets(), [
            'x_payments_subscription',
        ]);
    }
}
