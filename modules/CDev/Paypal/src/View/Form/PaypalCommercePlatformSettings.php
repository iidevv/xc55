<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Form;

/**
 * Paypal commerce platfrom settings form
 */
class PaypalCommercePlatformSettings extends \XLite\View\Form\AForm
{
    /**
     * Get default target field value
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'paypal_commerce_platform_settings';
    }

    /**
     * Get default action field value
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return parent::getClassName() . ' use-inline-error';
    }
}
