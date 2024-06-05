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
 * GdprConsent
 */
class GdprConsent extends \XLite\View\FormField\Input\Checkbox
{
    public const PARAM_FORCE_SHOW = 'forceShow';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_FORCE_SHOW => new \XLite\Model\WidgetParam\TypeBool('Show to all customers (regardless of country)', false),
        ];
    }

    /**
     * @return mixed
     */
    protected function shouldShowToAnyUser()
    {
        return $this->getParam(static::PARAM_FORCE_SHOW);
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/GDPR/form_field/checkbox/gdpr_consent.twig';
    }

    /**
     * @return string
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' gdpr-consent not-floating' . (
            $this->isPopup()
                ? ' open-as-separate-page'
                : ''
            );
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/GDPR/form_field/checkbox/gdpr_consent.js',
        ]);
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
        return true;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !Auth::getInstance()->isUserGdprConsent()
            && (Auth::getInstance()->isUserFromGdprCountry() || $this->shouldShowToAnyUser());
    }

    /**
     * @return bool|mixed
     */
    public function getValue()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'I consent to the collection and processing of my personal data';
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
     * @return bool
     */
    protected function isPopup()
    {
        return \XLite\Core\Request::getInstance()->isAJAX()
            && \XLite\Core\Request::getInstance()->widget
            && !in_array(\XLite\Core\Request::getInstance()->widget, [
                'XC\GDPR\View\Checkout\Fastlane\GdprConsent',
                'XC\GDPR\View\Checkout\GdprConsent',
            ], true);
    }

    /**
     * @return string
     */
    protected function getGdprLabelURL()
    {
        return PrivacyPolicy::getInstance()->isStaticPageAvailable() && $this->isPopup()
            ? PrivacyPolicy::getInstance()->getStaticPage()->getFrontURL()
            : $this->buildFullURL('privacy_policy', '', $this->isPopup()
                ? []
                : [
                    'widget' => '\XC\GDPR\View\Page\PrivacyPolicy',
                    'popup'  => '1',
                ]);
    }
}
