<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\MultiVendor\Service;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\MultiVendor")
 *
 */

class AuthService extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\AuthService
{
    const ACCESS_VENDOR = 'vendor';

    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return string
     */
    public function getAccessLevelForProfile(\XLite\Model\Profile $profile)
    {
        return $profile->isVendor()
            ? static::ACCESS_VENDOR
            : parent::getAccessLevelForProfile($profile);

    }
}
