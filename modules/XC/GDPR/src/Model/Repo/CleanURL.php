<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XC\GDPR\Core\PrivacyPolicy;

/**
 * CleanURL
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SimpleCMS")
 */
class CleanURL extends \XLite\Model\Repo\CleanURL
{
    public static function getConfigCleanUrlAliases()
    {
        $list = parent::getConfigCleanUrlAliases();

        if (!PrivacyPolicy::getInstance()->isStaticPageAvailable()) {
            $list += [
                'privacy_policy' => PrivacyPolicy::DEFAULT_CLEAN_URL,
            ];
        }

        return $list;
    }
}
