<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View;

class PaypalButton extends \XLite\View\Dialog
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'paypal_button';
        $list[] = 'paypal_commerce_platform_button';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/paypal_button';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getTarget() === 'paypal_commerce_platform_button'
            ? 'modules/CDev/Paypal/paypal_button/paypal_checkout.twig'
            : parent::getDefaultTemplate();
    }

    /**
     * @return bool
     */
    protected function isPaypalForMarketplaces()
    {
        return $this->getPaymentMethod()
            && $this->getPaymentMethod()->getServiceName() === \CDev\Paypal\Main::PP_METHOD_PFM;
    }
}
