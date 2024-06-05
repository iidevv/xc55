<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\View\StickyPanel\Payment;

/**
 * Payment method settings sticky panel
 */
class NonConfigured extends \XLite\View\StickyPanel\Payment\Settings
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = [];
        $list['connect'] = $this->getWidget(
            [],
            'XC\Stripe\View\Button\Connect'
        );
        $list = array_merge($list, parent::defineButtons());
        unset($list['save']);

        return $list;
    }
}
