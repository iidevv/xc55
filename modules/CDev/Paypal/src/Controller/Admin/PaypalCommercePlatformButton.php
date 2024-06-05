<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Controller\Admin;

use XLite\Core\Database;
use XLite\Model\Payment\Method;
use CDev\Paypal\Main as PaypalMain;
use CDev\Paypal\Core\PaypalCommercePlatform\Onboarding;

class PaypalCommercePlatformButton extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Paypal module string name for payment methods
     */
    public const MODULE_NAME = 'CDev_Paypal';

    /**
     * @return string
     */
    public function getTitle()
    {
        $paymentMethod = $this->getPaymentMethod();

        return $paymentMethod
            ? $paymentMethod->getName()
            : '';
    }

    /**
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'CDev\Paypal\View\Model\PaypalButton';
    }

    public function doActionUpdate()
    {
        $list = new \CDev\Paypal\View\ItemsList\Model\PaypalButton();
        $list->processQuick();

        $this->getModelForm()->performAction('update');
    }

    /**
     * @return Method
     */
    public function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = PaypalMain::getPaymentMethod(
                PaypalMain::PP_METHOD_PCP
            );
        }

        return $this->paymentMethod;
    }

    /**
     * @param null $returnUrl
     *
     * @return string
     * @throws \Exception
     */
    public function getSignUpUrl($returnUrl = null): string
    {
        \CDev\Paypal\Core\Lock\PaypalCommerceOnboardingLocker::getInstance()->lock('paypal_onboarding_return');

        $url = \XLite\Core\Cache\ExecuteCached::getCache(['\CDev\Paypal\View\Settings\PaypalCommercePlatformSettings::getSignUpUrl', $returnUrl]);

        if (empty($url)) {
            $onboarding = new Onboarding();

            $sellerNonce = $this->getSellerNonce();
            $returnUrl   = $returnUrl ?: $this->buildFullURL('paypal_commerce_platform_settings', 'onboarding_return');

            $url = $onboarding->generatePaypalSignUpLink($sellerNonce, $returnUrl);

            if ($url) {
                $url .= '&displayMode=minibrowser';

                \XLite\Core\Cache\ExecuteCached::setCache(
                    ['\CDev\Paypal\View\Settings\PaypalCommercePlatformSettings::getSignUpUrl', $returnUrl],
                    $url,
                    3600
                );
            }
        }

        return $url;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getSellerNonce(): string
    {
        $paymentMethod = $this->getPaymentMethod();
        $sellerNonce   = $paymentMethod->getSetting('sellerNonce');

        if (!$sellerNonce) {
            $sellerNonce = hash('sha512', time());

            $paymentMethod->setSetting('sellerNonce', $sellerNonce);

            Database::getEM()->flush();
        }

        return $sellerNonce;
    }
}
