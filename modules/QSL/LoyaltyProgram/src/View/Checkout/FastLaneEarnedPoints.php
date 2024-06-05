<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Checkout;

use XCart\Extender\Mapping\ListChild;

/**
 * Loyalty earned points widget in FLC
 *
 * @ListChild (list="checkout_fastlane.sections.details.right", weight="14")
 */
class FastLaneEarnedPoints extends \XLite\View\AView
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/LoyaltyProgram/checkout/steps/review/parts/items.earned_points.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/LoyaltyProgram/checkout/steps/review/parts/items.earned_points.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/LoyaltyProgram/checkout/steps/review/parts/items.earned_points.js';

        return $list;
    }
}
