<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Core\Mail;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Registry extends \XLite\Core\Mail\Registry
{
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::ZONE_CUSTOMER => [
                'modules/XC/VendorMessages/new_message_notification' => NewMessageNotification::class,
                'modules/XC/VendorMessages/notification'             => OrderMessageNotification::class,
            ],
        ]);
    }
}
