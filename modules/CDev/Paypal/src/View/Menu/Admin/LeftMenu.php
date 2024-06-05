<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['payment_settings'])) {
            $this->relatedTargets['payment_settings'] = [];
        }

        $this->relatedTargets['payment_settings'][] = 'paypal_settings';
        $this->relatedTargets['payment_settings'][] = 'paypal_credit';
        $this->relatedTargets['payment_settings'][] = 'paypal_button';
        $this->relatedTargets['payment_settings'][] = 'paypal_commerce_platform_settings';
        $this->relatedTargets['payment_settings'][] = 'paypal_commerce_platform_credit';
        $this->relatedTargets['payment_settings'][] = 'paypal_commerce_platform_button';

        parent::__construct($params);
    }
}
