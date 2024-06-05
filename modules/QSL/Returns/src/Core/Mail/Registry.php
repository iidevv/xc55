<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Core\Mail;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Registry extends \XLite\Core\Mail\Registry
{
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::ZONE_CUSTOMER => [
                'modules/QSL/Returns/return/created' => OrderReturnCreated::class,
                'modules/QSL/Returns/return/completed' => OrderReturnCompleted::class,
                'modules/QSL/Returns/return/declined' => OrderReturnDeclined::class,
            ],
            \XLite::ZONE_ADMIN => [
                'modules/QSL/Returns/return/created' => OrderReturnCreatedAdmin::class,
            ],
        ]);
    }
}
