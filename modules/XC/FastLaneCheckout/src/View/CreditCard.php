<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CreditCard extends \XLite\View\CreditCard
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file' => 'modules/XC/FastLaneCheckout/checkout_fastlane/credit_card_fastlane.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
