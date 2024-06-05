<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    protected $isDuringActionCheckout = false;

    /**
     * @return bool
     */
    protected function checkReviewStep()
    {
        return $this->isIframeFLCHackRequired() || $this->isDuringActionCheckout
            ? parent::checkReviewStep()
            : parent::checkReviewStep() && $this->isGDPRConsent();
    }

    /**
     * Should be removed once XPayments team will do proper loading of iframe
     *
     * @return bool
     */
    protected function isIframeFLCHackRequired()
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = \XLite::getController()->getCart();

        $xpaymentsEnabled = \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('CDev-XPaymentsConnector');

        return $cart
            && $xpaymentsEnabled
            && \XLite\Core\Request::getInstance()->isAJAX()
            && \XLite\Core\Config::getInstance()->General->checkout_type === 'fast-lane'
            && $cart->getPaymentProcessor() instanceof \CDev\XPaymentsConnector\Model\Payment\Processor\AXPayments;
    }

    protected function doActionCheckout()
    {
        $this->isDuringActionCheckout = true;
        parent::doActionCheckout();
        $this->isDuringActionCheckout = false;
    }

    protected function doPayment()
    {
        $this->updateGDPRConsent();

        parent::doPayment();
    }

    /**
     * Update profile gdprConsent field
     */
    protected function updateGDPRConsent()
    {
        $this->getCart()->getProfile()->setGdprConsent(true);

        if (
            \XLite\Core\Request::getInstance()->gdprConsent
            && $this->getCart()->getOrigProfile()
        ) {
            $this->getCart()->getOrigProfile()->setGdprConsent(true);
        }

        $cookies = Request::getInstance()->getCookieData();

        if (
            Auth::getInstance()->isDefaultCookiesConsentSet()
            && $cookies['consent_default'] !== $this->getCart()->getProfile()->isDefaultCookiesConsent()
        ) {
            $this->getCart()->getProfile()->setDefaultCookiesConsent($cookies['consent_default']);

            if ($this->getCart()->getOrigProfile()) {
                $this->getCart()->getOrigProfile()->setDefaultCookiesConsent($cookies['consent_default']);
            }
        }

        if (
            Auth::getInstance()->isAllCookiesConsentSet()
            && $cookies['consent_all'] !== $this->getCart()->getProfile()->isAllCookiesConsent()
        ) {
            $this->getCart()->getProfile()->setAllCookiesConsent($cookies['consent_all']);

            if ($this->getCart()->getOrigProfile()) {
                $this->getCart()->getOrigProfile()->setAllCookiesConsent($cookies['consent_all']);
            }
        }
    }

    /**
     * Check if customer is gdpr consent
     *
     * @return bool
     */
    protected function isGDPRConsent()
    {
        return \XLite\Core\Request::getInstance()->gdprConsent
            || $this->getCart()->getProfile()->isGdprConsent()
            || (
                $this->getCart()->getOrigProfile()
                && $this->getCart()->getOrigProfile()->isGdprConsent()
            );
    }
}
