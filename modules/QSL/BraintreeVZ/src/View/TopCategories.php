<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BraintreeVZ\View;

use XCart\Extender\Mapping\Extender;

/**
 * TopCategories decorator
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\CrispWhiteSkin")
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
            'braintree_credit_cards',
        ]);
    }
}
