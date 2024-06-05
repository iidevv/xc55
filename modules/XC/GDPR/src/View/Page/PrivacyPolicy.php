<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Page;

use XCart\Extender\Mapping\ListChild;

/**
 * CookiePolicy
 *
 * @ListChild (list="center", zone="customer")
 */
class PrivacyPolicy extends \XLite\View\AView
{
    public static function getAllowedTargets()
    {
        return [
            'privacy_policy'
        ];
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/GDPR/privacy_policy/style.min.css'
        ]);
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/GDPR/privacy_policy/body.twig';
    }

    protected function getPageContent()
    {
        return \XC\GDPR\Core\PrivacyPolicy::getInstance()->isStaticPageAvailable()
            ? \XC\GDPR\Core\PrivacyPolicy::getInstance()->getStaticPage()->getBody()
            : static::t('Privacy policy page text');
    }
}
