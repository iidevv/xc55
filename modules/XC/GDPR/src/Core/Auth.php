<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Request;
use XLite\Model\Profile;

/**
 * Auth decorator
 * @Extender\Mixin
 */
class Auth extends \XLite\Core\Auth
{
    /**
     * @return bool
     */
    public function isUserGdprConsent()
    {
        return $this->getProfile()
            && $this->getProfile()->isGdprConsent();
    }

    /**
     * @return bool
     */
    public function isUserCookiesConsent()
    {
        return $this->isUserAllCookiesConsent()
            || $this->isUserDefaultCookiesConsent(true);
    }


    /**
     * Use default for empty / not accepted cookie params
     *
     * @param bool $ignoreAll ignore all / default empty check
     *
     * @return bool
     */
    public function isUserDefaultCookiesConsent($ignoreAll = false)
    {
        $defaultCookies = $this->isDefaultCookiesConsentSet() || (
            $this->getProfile()
            && $this->getProfile()->isDefaultCookiesConsent()
        );

        return $defaultCookies ||
            (
                !$ignoreAll
                && $this->isUserFromGdprCountry()
                && Config::getInstance()->XC->GDPR->show_cookie_popup
                && !$defaultCookies
                && !$this->isUserAllCookiesConsent()
            );
    }

    /**
     * @return bool
     */
    public function isUserAllCookiesConsent()
    {
        return
            $this->isAllCookiesConsentSet()
            || (
                $this->getProfile()
                && $this->getProfile()->isAllCookiesConsent()
            );
    }

    /**
     * @return bool
     */
    public function isDefaultCookiesConsentSet()
    {
        $cookies = Request::getInstance()->getCookieData();
        return isset($cookies['consent_default']) && $cookies['consent_default'] === static::getCookieHash();
    }

    /**
     * @return bool
     */
    public function isAllCookiesConsentSet()
    {
        $cookies = Request::getInstance()->getCookieData();
        return isset($cookies['consent_all']) && $cookies['consent_all'] === static::getCookieHash();
    }

    /**
     * @return bool
     */
    public function isUserFromGdprCountry()
    {
        return $this->isAnyCountrySuitable() || $this->isUserAddressSuitable();
    }

    /**
     * @return bool
     */
    public function isUserAddressSuitable()
    {
        $countryCode = $this->getProfile() && $this->getProfile()->getBillingAddress() && $this->getProfile()->getBillingAddress()->getCountry()
            ? $this->getProfile()->getBillingAddress()->getCountry()->getCode()
            : null;

        if (!$countryCode && !\XLite::isAdminZone() && \XLite::getController()->getCart()) {
            /** @var \XLite\Model\Profile $cartProfile */
            $cartProfile = \XLite::getController()->getCart()->getProfile();
            $countryCode = $cartProfile && $cartProfile->getBillingAddress() && $cartProfile->getBillingAddress()->getCountry()
                ? $cartProfile->getBillingAddress()->getCountry()->getCode()
                : null;
        }

        if (!$countryCode) {
            $address = \XLite\Model\Shipping::getDefaultAddress();
            $countryCode = $address['country'];
        }

        return $countryCode ? $this->isCountrySelected($countryCode) : true;
    }

    /**
     * @return bool
     */
    protected function isAnyCountrySuitable()
    {
        return !count($this->getAllowedCountriesList());
    }

    /**
     * TODO: move to core
     *
     * @param $countryCode
     *
     * @return bool
     */
    protected function isCountrySelected($countryCode)
    {
        return in_array(mb_strtoupper($countryCode), $this->getAllowedCountriesList(), true);
    }

    /**
     * @return array
     */
    protected function getAllowedCountriesList()
    {
        $v = trim(Config::getInstance()->XC->GDPR->geoip_countries);
        return strlen($v)
            ? array_map('trim', explode(',', $v))
            : [];
    }

    /**
     * @param Profile $profile
     *
     * @return boolean
     */
    public function loginProfile(Profile $profile, $withCells = true)
    {
        $result = parent::loginProfile($profile, $withCells);
        if ($result) {
            $updateIsNeeded = false;
            $cookies = Request::getInstance()->getCookieData();

            if (
                $this->isDefaultCookiesConsentSet()
                && $cookies['consent_default'] !== $profile->isDefaultCookiesConsent()
            ) {
                $profile->setDefaultCookiesConsent($cookies['consent_default']);
                $updateIsNeeded = true;
            }

            if (
                $this->isAllCookiesConsentSet()
                && $cookies['consent_all'] !== $profile->isAllCookiesConsent()
            ) {
                $profile->setAllCookiesConsent($cookies['consent_all']);
                $updateIsNeeded = true;
            }

            if ($updateIsNeeded) {
                \XLite\Core\Database::getEM()->persist($profile);
                \XLite\Core\Database::getEM()->flush();
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function logoff()
    {
        parent::logoff();

        $request = \XLite\Core\Request::getInstance();
        $request->unsetCookie('consent_default');
        $request->unsetCookie('consent_all');
    }

    /**
     * Saved hash
     *
     * @return string
     */
    public static function getCookieHash()
    {
        $config = \XLite\Core\Config::getInstance()->XC->GDPR;

        return $config->show_cookie_popup ? $config->cookie_hash : 'disabled';
    }
}
