<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Model\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * Class represents an order
 * @Extender\Mixin
 */
class Shipping extends \XLite\Model\Order\Status\Shipping
{
    public const STATUS_NOT_FINISHED = 'NF';

    public static function getDisallowedToSetManuallyStatuses()
    {
        return array_merge(parent::getDisallowedToSetManuallyStatuses(), [
            static::STATUS_NOT_FINISHED,
        ]);
    }

    /**
     * List of change status handlers;
     * top index - old status, second index - new one
     * (<old_status> ----> <new_status>: $statusHandlers[$old][$new])
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        return array_merge_recursive(parent::getStatusHandlers(), [
            self::STATUS_NOT_FINISHED => [
                self::STATUS_NEW              => ['NFOCreated'],
                self::STATUS_PROCESSING       => ['NFOCreated'],
                self::STATUS_SHIPPED          => ['NFOCreated', 'ship'],
                self::STATUS_DELIVERED        => ['NFOCreated'],
                self::STATUS_WILL_NOT_DELIVER => ['NFOCreated'],
                self::STATUS_RETURNED         => ['NFOCreated'],
            ],
        ]);
    }
}
