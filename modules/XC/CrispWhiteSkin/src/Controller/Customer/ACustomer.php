<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XC\CrispWhiteSkin;
use XLite\Core\Request;
use XLite\Core\Session;

/**
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    public const SELECTED_COUNTRY_CODE_SESSION_NAME = 'selectedCountryCode';

    /**
     * Get current selected country if available
     *
     * @return \XLite\Model\Country
     */
    public function getCurrentCountry()
    {
        $result = null;

        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            $result = \XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedCountry();
        } elseif (Session::getInstance()->get(self::SELECTED_COUNTRY_CODE_SESSION_NAME)) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy([
                'code' => Session::getInstance()->get(self::SELECTED_COUNTRY_CODE_SESSION_NAME)
            ]);
        } elseif (Auth::getInstance()->getProfile() && Auth::getInstance()->getProfile()->getShippingAddress()) {
            $result = Auth::getInstance()->getProfile()->getShippingAddress()->getCountry();
        } elseif (CrispWhiteSkin\Main::isModuleEnabled('XC\Geolocation')) {
            $result = \XLite\Model\Address::getDefaultFieldValue('country');
        }

        return $result;
    }

    /**
     * Get current selected currency if available
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrentCurrency()
    {
        $currency = null;

        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            $currency = \XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedMultiCurrency();
        }

        return $currency;
    }

    /**
     * Return true if there are active currencies for currency selector
     *
     * @return boolean
     */
    public function isCurrencySelectorAvailable()
    {
        $result = false;

        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            $result = \XC\MultiCurrency\Core\MultiCurrency::getInstance()->hasMultipleCurrencies();
        }

        return $result;
    }

    /**
     * Return profile email
     *
     * @return null|string
     */
    public function getProfileLogin()
    {
        return Auth::getInstance()->getProfile()
            ? Auth::getInstance()->getProfile()->getLogin()
            : null;
    }

    /**
     * Check if additional mobile breadcrumbs are shown
     *
     * @return boolean
     */
    public function isShowAdditionalMobileBreadcrumbs()
    {
        return false;
    }

    /**
     * Change current language
     *
     * @return void
     */
    protected function doActionChangeLanguage()
    {
        parent::doActionChangeLanguage();

        $request = Request::getInstance();
        Session::getInstance()->set(
            self::SELECTED_COUNTRY_CODE_SESSION_NAME,
            $request->country_code
        );
    }
}
