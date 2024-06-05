<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\View\StickyPanel\Payment;

/**
 * Payment method settings sticky panel
 */
class Configured extends \XLite\View\StickyPanel\Payment\Settings
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $backToModules = $list['addons-list'];
        unset($list['addons-list']);
        $list['disconnect'] = $this->getWidget(
            [
                'style'    => 'regular-button action always-enabled',
                'label'    => 'Disconnect',
                'location' => $this->buildURL('stripe_oauth', 'disconnect')
            ],
            'XLite\View\Button\Link'
        );
        $list['addons-list'] = $backToModules;

        return $list;
    }
}
