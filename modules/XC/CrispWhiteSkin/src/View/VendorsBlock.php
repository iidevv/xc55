<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * TopCategories decorator
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
abstract class VendorsBlock extends \XC\MultiVendor\View\VendorsBlock
{
    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return [
            'order_list',
            'order',
            'address_book',
            'mailchimp_subscriptions',
            'profile',
            'contact_us',
            'messages',
            'login',
            'recover_password',
        ];
    }
}
