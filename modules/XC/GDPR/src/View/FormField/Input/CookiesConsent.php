<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\FormField\Input;

use XLite\Core\Auth;
use XC\GDPR\Core\PrivacyPolicy;
use XC\GDPR\Main;

/**
 * CookiesConsent
 */
class CookiesConsent extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/GDPR/form_field/checkbox/cookies_consent.twig';
    }

    /**
     * @return string
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' gdpr-consent not-floating';
    }

    /**
     * lazyload on register popup can be load css only
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $file = 'modules/XC/GDPR/form_field/checkbox/gdpr_consent.min.css';

        if (Main::isCrispWhiteBasedSkinEnabled()) {
            $file = 'modules/XC/GDPR/form_field/checkbox/gdpr_consent_crispwhite.min.css';
        }

        return array_merge(parent::getCSSFiles(), [
            $file,
        ]);
    }

    /**
     * @return bool
     */
    protected function isRequired()
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite::getController()->getTarget() === 'profile' && \XLite\Core\Auth::getInstance()->isLogged()
            && Auth::getInstance()->isUserFromGdprCountry();
    }

    /**
     * @return bool|mixed
     */
    public function getValue()
    {
        return true;
    }

    /**
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if ($this->isChecked() !== $value) {
            $request = \XLite\Core\Request::getInstance();
            $request->unsetCookie('consent_default');
            $request->unsetCookie('consent_all');
        }

        parent::setValue($value);
    }

    /**
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'I consent to the processing of all cookies';
    }

    /**
     * @return array
     */
    protected function getDefaultLabelParams()
    {
        return [
            'url' => $this->getGdprLabelURL(),
        ];
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        return $profile && $profile->isAllCookiesConsent();
    }

    /**
     * @return string
     */
    protected function getGdprLabelURL()
    {
        return PrivacyPolicy::getInstance()->isStaticPageAvailable()
            ? PrivacyPolicy::getInstance()->getStaticPage()->getFrontURL()
            : $this->buildFullURL('privacy_policy', '', []);
    }
}
