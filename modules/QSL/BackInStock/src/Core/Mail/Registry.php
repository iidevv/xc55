<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Core\Mail;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Registry extends \XLite\Core\Mail\Registry
{
    /**
     * @inheritDoc
     */
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::ZONE_CUSTOMER => [
                NotificationStock::MESSAGE_DIR => NotificationStock::class,
                NotificationPrice::MESSAGE_DIR => NotificationPrice::class,
            ]
        ]);
    }
}
