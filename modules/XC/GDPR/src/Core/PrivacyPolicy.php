<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core;

/**
 * PrivacyPolicy
 */
class PrivacyPolicy extends \XLite\Base\Singleton
{
    public const DEFAULT_CLEAN_URL = 'privacy-policy';

    /**
     * @return bool
     */
    public function isStaticPageAvailable()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isNeedToCreateStaticPage()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getStaticPage()
    {
        return false;
    }
}
