<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button;

/**
 * Sign up button
 */
class SignUp extends \XLite\View\Button\SimpleLink
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/settings/signup.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $api = \CDev\Paypal\Main::getRESTAPIInstance();
        if ($api->isInContextSignUpAvailable()) {
            $list[] = 'modules/CDev/Paypal/settings/signup.js';
        }

        return $list;
    }

    /**
     * Get CSS class name
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . 'btn regular-button pp-signup';
    }

    /**
     * Defines the default location path
     *
     * @return string
     */
    protected function getDefaultLocation()
    {
        $api = \CDev\Paypal\Main::getRESTAPIInstance();
        $method = \CDev\Paypal\Main::getPaymentMethod(\CDev\Paypal\Main::PP_METHOD_EC);

        return $api->isInContextSignUpAvailable()
            ? $method->getReferralPageURL($method)
            : $this->buildURL('paypal_settings', '', ['method_id' => $method->getMethodId()]);
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getDefaultAttributes()
    {
        $params = [
            'target'             => 'PPFrame',
            'data-paypal-button' => 'true',
        ];

        $api = \CDev\Paypal\Main::getRESTAPIInstance();

        return $api->isInContextSignUpAvailable()
            ? $params
            : [];
    }
}
