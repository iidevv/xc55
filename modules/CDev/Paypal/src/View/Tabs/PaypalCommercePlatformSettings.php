<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Tabs;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Config;

/**
 * Tabs related to paypal settings page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PaypalCommercePlatformSettings extends \XLite\View\Tabs\APaymentMethodTabs
{
    /**
     * @var \XLite\Model\Payment\Method
     */
    protected $paymentMethod;

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'paypal_commerce_platform_settings';

        if (in_array(Config::getInstance()->Company->location_country, \CDev\Paypal\Main::PP_PAYPAL_CREDIT_COUNTRIES, true)) {
            $list[] = 'paypal_commerce_platform_credit';
        }

        $list[] = 'paypal_commerce_platform_button';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = [
            'paypal_commerce_platform_settings' => [
                'weight' => 100,
                'title'  => static::t('Settings'),
                'widget' => 'CDev\Paypal\View\Settings\PaypalCommercePlatformSettings',
            ],
            'paypal_commerce_platform_credit' => [
                'weight' => 200,
                'title'  => static::t('PayPal Credit'),
                'widget' => 'CDev\Paypal\View\Settings',
            ],
            'paypal_commerce_platform_button' => [
                'weight' => 300,
                'title'  => static::t('Customize the PayPal button'),
                'widget' => 'CDev\Paypal\View\PaypalButton',
            ],
        ];

        if (!in_array(Config::getInstance()->Company->location_country, \CDev\Paypal\Main::PP_PAYPAL_CREDIT_COUNTRIES, true)) {
            unset($tabs['paypal_commerce_platform_credit']);
        }

        return $tabs;
    }

    /**
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = \CDev\Paypal\Main::getPaymentMethod(
                \CDev\Paypal\Main::PP_METHOD_PCP
            );
        }

        return $this->paymentMethod;
    }
}
