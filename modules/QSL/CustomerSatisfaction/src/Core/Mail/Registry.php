<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Core\Mail;

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
                    'modules/QSL/CustomerSatisfaction/customer_notification' => CustomerSatisfactionNotification::class,
            ],
            \XLite::ZONE_ADMIN => [
                'modules/QSL/CustomerSatisfaction/feedback' => CustomerSatisfactionFeedbackMessage::class,
            ],
        ]);
    }
}
