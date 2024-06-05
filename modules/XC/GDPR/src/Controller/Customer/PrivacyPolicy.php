<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Controller\Customer;

use XC\GDPR\Core\PrivacyPolicy as PrivacyPolicyUnit;

/**
 * PrivacyPolicy
 */
class PrivacyPolicy extends \XLite\Controller\Customer\ACustomer
{
    public function getTitle()
    {
        return \XC\GDPR\Core\PrivacyPolicy::getInstance()->isStaticPageAvailable()
            ? \XC\GDPR\Core\PrivacyPolicy::getInstance()->getStaticPage()->getName()
            : static::t('Privacy statement');
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        if (
            !\XLite\Core\Request::getInstance()->popup
            && PrivacyPolicyUnit::getInstance()->isStaticPageAvailable()
        ) {
            $this->redirect(
                html_entity_decode(PrivacyPolicyUnit::getInstance()->getStaticPage()->getFrontURL()),
                302
            );
        }
    }

    protected function doActionConsentAll()
    {
        \XLite\Core\Request::getInstance()->setCookie('consent_all', \XLite\Core\Auth::getCookieHash(), $this->getConsentTtl());

        if ($profile = \XLite\Core\Auth::getInstance()->getProfile()) {
            $profile->setAllCookiesConsent(true);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    protected function doActionConsentDefault()
    {
        \XLite\Core\Request::getInstance()->setCookie('consent_default', \XLite\Core\Auth::getCookieHash(), $this->getConsentTtl());

        if ($profile = \XLite\Core\Auth::getInstance()->getProfile()) {
            $profile->setDefaultCookiesConsent(true);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * @return int Days
     */
    protected function getConsentTtl()
    {
        if (\XLite\Core\Config::getInstance()->XC->GDPR->cookie_consent_ttl_type === 'session') {
            return 0;
        }

        return 86400 * (int)\XLite\Core\Config::getInstance()->XC->GDPR->cookie_consent_ttl;
    }
}
