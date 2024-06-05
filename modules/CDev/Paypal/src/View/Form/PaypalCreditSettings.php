<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Form;

/**
 * Paypal settings form
 */
class PaypalCreditSettings extends \CDev\Paypal\View\Form\Settings
{
    /**
     * Get default target field value
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        if (\XLite::getController() instanceof \CDev\Paypal\Controller\Admin\PaypalCommercePlatformCredit) {
            return 'paypal_commerce_platform_credit';
        }

        return 'paypal_credit';
    }
}
