<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal;

use Includes\Utils\Module\Manager;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Paypal methods service names
     */
    public const PP_METHOD_PPA  = 'PaypalAdvanced';
    public const PP_METHOD_PFL  = 'PayflowLink';
    public const PP_METHOD_PFTR = 'PayflowTransparentRedirect';
    public const PP_METHOD_EC   = 'ExpressCheckout';
    public const PP_METHOD_PPS  = 'PaypalWPS';
    public const PP_METHOD_PC   = 'PaypalCredit';
    public const PP_METHOD_PAD  = 'PaypalAdaptive';
    public const PP_METHOD_PFM  = 'PaypalForMarketplaces';
    public const PP_METHOD_PCP  = 'PaypalCommercePlatform';

    /**
     * List of merchant countries where Pay Later(Paypal Credit) can work
     * https://developer.paypal.com/docs/business/pay-later/au/
     */
    public const PP_PAYPAL_CREDIT_COUNTRIES = ['US', 'AU', 'FR', 'GB', 'DE'];

    /**
     * RESTAPI instance
     *
     * @var \CDev\Paypal\Core\RESTAPI
     */
    protected static $RESTAPI;

    /**
     * Payment methods
     *
     * @var \XLite\Model\Payment\Method[]
     */
    protected static $paymentMethod = [];

    /**
     * Defines the link for the payment settings form
     *
     * @return string
     */
    public static function getPaymentSettingsForm()
    {
        return Manager::getRegistry()->getModuleSettingsUrl('CDev', 'Paypal');
    }

    /**
     * Returns payment method
     *
     * @param string  $serviceName Service name
     * @param boolean $enabled     Enabled status OPTIONAL
     *
     * @return \XLite\Model\Payment\Method
     */
    public static function getPaymentMethod($serviceName, $enabled = null)
    {
        if (!isset(static::$paymentMethod[$serviceName])) {
            static::$paymentMethod[$serviceName] = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['service_name' => $serviceName]);
            if (!static::$paymentMethod[$serviceName]) {
                static::$paymentMethod[$serviceName] = false;
            }
        }

        return static::$paymentMethod[$serviceName]
        && (
            is_null($enabled)
            || static::$paymentMethod[$serviceName]->getEnabled() === (bool) $enabled
        )
            ? static::$paymentMethod[$serviceName]
            : null;
    }

    /**
     * Returns true if ExpressCheckout payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isExpressCheckoutEnabled($order = null)
    {
        static $result;

        $index = ($order !== null) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_EC, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if ForMarketplaces payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalForMarketplacesEnabled($order = null)
    {
        static $result;

        $index = ($order !== null) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PFM, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if CommercePlatform payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalCommercePlatformEnabled($order = null)
    {
        static $result;

        $index = ($order !== null) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PCP, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if Advanced payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalAdvancedEnabled($order = null)
    {
        static $result;

        $index = ($order !== null) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PPA, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Returns BuyNow button availability status
     *
     * @return boolean
     */
    public static function isBuyNowEnabled()
    {
        static $result;

        if ($result === null) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_EC, true) ?? static::getPaymentMethod(static::PP_METHOD_PCP, true);
            if ($paymentMethod) {
                $result = (bool) $paymentMethod->getSetting('buyNowEnabled');
            }
        }

        return $result;
    }

    /**
     * Returns Header badge availability status
     *
     * @return boolean
     */
    public static function isHeadIconEnabled()
    {
        static $result;

        if ($result === null) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_EC, true) ?? static::getPaymentMethod(static::PP_METHOD_PCP, true);
            if ($paymentMethod) {
                $result = (bool) $paymentMethod->getSetting('headIconEnabled');
            }
        }

        return $result;
    }

    /**
     * Returns true if PaypalCredit payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalCreditEnabled($order = null)
    {
        static $result;

        $index = ($order !== null ? 1 : 0);

        if (!isset($result[$index])) {
            if (in_array(\XLite\Core\Config::getInstance()->Company->location_country, static::PP_PAYPAL_CREDIT_COUNTRIES, true)) {
                $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PC, true);
                $result[$index] = $paymentMethod
                    && $paymentMethod->isEnabled()
                    && $paymentMethod->getSetting('enabled')
                    && static::isExpressCheckoutEnabled($order);
            } else {
                $result[$index] = false;
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if PaypalCredit payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalCreditForCommercePlatformEnabled($order = null)
    {
        static $result;

        $index = ($order !== null ? 1 : 0);

        if (!isset($result[$index])) {
            if (
                in_array(\XLite\Core\Config::getInstance()->Company->location_country, static::PP_PAYPAL_CREDIT_COUNTRIES, true)
                && static::isPaypalCommercePlatformEnabled($order)
            ) {
                $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PC);
                $result[$index] = $paymentMethod
                    && $paymentMethod->getSetting('enabled');
            } else {
                $result[$index] = false;
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if PaypalWPS payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalWPSEnabled($order = null)
    {
        static $result;

        $index = ($order !== null) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PPS, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();
        }

        return $result[$index];
    }

    /**
     * Returns true if PaypalAdaptive payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalAdaptiveEnabled($order = null)
    {
        static $result;

        $index = ($order !== null) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PAD, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();
        }

        return $result[$index];
    }

    /**
     * Get logo
     *
     * @return string
     */
    public static function getLogo()
    {
        return \XLite\Core\URLManager::getShopURL(
            \XLite\Core\Layout::getInstance()->getLogo(),
            true,
            [],
            \XLite\Core\URLManager::URL_OUTPUT_FULL,
            false
        );
    }

    /**
     * Get logo
     *
     * @return string
     */
    public static function getSignUpLogo()
    {
        $logo = \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'modules/CDev/Paypal/signup_logo.png',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            \XLite::INTERFACE_WEB,
            \XLite::ZONE_ADMIN
        );

        return \XLite\Core\URLManager::getShopURL(
            $logo,
            true,
            [],
            \XLite\Core\URLManager::URL_OUTPUT_FULL,
            false
        );
    }

    /**
     * Return RESTAPI instance
     *
     * @return \CDev\Paypal\Core\RESTAPI
     */
    public static function getRESTAPIInstance()
    {
        if (static::$RESTAPI === null) {
            static::$RESTAPI = new \CDev\Paypal\Core\RESTAPI();
        }

        return static::$RESTAPI;
    }

    /**
     * Returns paypal methods service codes
     *
     * @return array
     */
    public static function getServiceCodes()
    {
        return [
            static::PP_METHOD_PPA,
            static::PP_METHOD_PFL,
            static::PP_METHOD_EC,
            static::PP_METHOD_PPS,
            static::PP_METHOD_PC,
            static::PP_METHOD_PAD,
            static::PP_METHOD_PFM,
            static::PP_METHOD_PCP,
        ];
    }
}
