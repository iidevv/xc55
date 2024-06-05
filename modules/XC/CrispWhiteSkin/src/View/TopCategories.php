<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * TopCategories decorator
 * @Extender\Mixin
 */
abstract class TopCategories extends \XLite\View\TopCategories
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
            'address_book',
            'ordered_files',
            'mailchimp_subscriptions',
            'profile',
            'messages',
            'login',
            'recover_password',
            'saved_cards',
            'contact_us',
            'order',
        ];
    }
}
