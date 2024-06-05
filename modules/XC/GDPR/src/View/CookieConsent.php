<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\Core\Config;
use XLite\Core\Request;
use XC\GDPR\Core\PrivacyPolicy;
use XC\GDPR\Main;

/**
 * @ListChild(list="center", zone="customer", weight="9500")
 */
class CookieConsent extends \XLite\View\AView
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && Config::getInstance()->XC->GDPR->show_cookie_popup
            && !Request::getInstance()->isAJAX()
            && !Auth::getInstance()->isUserCookiesConsent()
            && Auth::getInstance()->isUserFromGdprCountry();
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/GDPR/cookie_consent/body.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $file = 'modules/XC/GDPR/cookie_consent/style.less';

        if (Main::isCrispWhiteBasedSkinEnabled()) {
            $file = 'modules/XC/GDPR/cookie_consent/style_crispwhite.less';
        }

        return array_merge(parent::getCSSFiles(), [
            [
                'file'  => $file,
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ],
        ]);
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/GDPR/cookie_consent/script.js',
        ]);
    }

    /**
     * @return string
     */
    protected function getConsentNote()
    {
        return static::t('Cookie consent text', [
            'store' => \XLite\Core\Config::getInstance()->Company->company_name,
            'url'   => $this->getPrivacyPolicyURL(),
        ]);
    }

    /**
     * @return string
     */
    protected function getPrivacyPolicyURL()
    {
        return PrivacyPolicy::getInstance()->isStaticPageAvailable()
            ? PrivacyPolicy::getInstance()->getStaticPage()->getFrontURL()
            : $this->buildFullURL('privacy_policy');
    }
}
