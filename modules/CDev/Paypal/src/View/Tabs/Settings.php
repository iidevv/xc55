<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to paypal settings page
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Settings extends \XLite\View\Tabs\APaymentMethodTabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'paypal_settings';

        if (in_array(\XLite\Core\Config::getInstance()->Company->location_country, \CDev\Paypal\Main::PP_PAYPAL_CREDIT_COUNTRIES, true)) {
            $list[] = 'paypal_credit';
        }

        $list[] = 'paypal_button';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'paypal_settings' => [
                'weight' => 100,
                'title'  => static::t('Settings'),
                'widget' => 'CDev\Paypal\View\Settings',
            ],
            'paypal_credit' => [
                'weight' => 200,
                'title'  => static::t('PayPal Credit'),
                'widget' => 'CDev\Paypal\View\Settings',
            ],
            'paypal_button' => [
                'weight' => 300,
                'title'  => static::t('Customize the PayPal button'),
                'widget' => 'CDev\Paypal\View\PaypalButton',
            ],
        ];
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        $controller = \XLite::getController();
        $rightController = ($controller instanceof \CDev\Paypal\Controller\Admin\PaypalSettings
            || $controller instanceof \CDev\Paypal\Controller\Admin\PaypalButton)
            && !($controller instanceof \CDev\Paypal\Controller\Admin\PaypalCredit);

        if (
            $rightController
            && $this->getPaymentMethod()
            && (!in_array(\XLite\Core\Config::getInstance()->Company->location_country, \CDev\Paypal\Main::PP_PAYPAL_CREDIT_COUNTRIES, true)
                || $this->getPaymentMethod()->getServiceName() === \CDev\Paypal\Main::PP_METHOD_PC
                || $this->getPaymentMethod()->getServiceName() === \CDev\Paypal\Main::PP_METHOD_PFM
                || $this->getPaymentMethod()->getServiceName() === \CDev\Paypal\Main::PP_METHOD_PCP
            )
        ) {
            unset($this->tabs['paypal_credit']);
        }

        return parent::prepareTabs();
    }

    /**
     * Returns an URL to a tab
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL($target, '', ['method_id' => \XLite\Core\Request::getInstance()->method_id]);
    }
}
