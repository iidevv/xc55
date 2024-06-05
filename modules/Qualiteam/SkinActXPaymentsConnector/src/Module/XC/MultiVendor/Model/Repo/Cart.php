<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Module\XC\MultiVendor\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Profile;

/**
 * Cart repository extension
 *
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActXPaymentsConnector","XC\MultiVendor"})
 */
class Cart extends \XLite\Model\Repo\Cart
{
    /**
     * Alter default findOneByProfile() method to select cart only with empty vendor and non zero-auth flag
     *
     * @param Profile $profile Profile object
     *
     * @return \XLite\Model\Cart
     */
    public function findOneByProfile($profile)
    {
        return parent::findOneBy(array('profile' => $profile, 'vendor' => null, 'is_zero_auth' => false));
    }
}
