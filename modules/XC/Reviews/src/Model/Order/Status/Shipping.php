<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping status
 * @Extender\Mixin
 */
abstract class Shipping extends \XLite\Model\Order\Status\Shipping
{
    /**
     * @inheritdoc
     */
    public static function getStatusHandlers()
    {
        return array_merge_recursive(static::getModuleStatusHandlers(), parent::getStatusHandlers());
    }

    /**
     * @return array
     */
    public static function getModuleStatusHandlers()
    {
        $handlers = [];
        $statuses = [
            static::STATUS_NEW,
            static::STATUS_PROCESSING,
            static::STATUS_SHIPPED,
            static::STATUS_RETURNED,
            static::STATUS_WAITING_FOR_APPROVE,
            static::STATUS_WILL_NOT_DELIVER,
            static::STATUS_NEW_BACKORDERED,
        ];

        foreach ($statuses as $status) {
            if (!isset($handlers[$status])) {
                $handlers[$status] = [
                    static::STATUS_DELIVERED => [],
                ];
            }
            array_unshift(
                $handlers[$status][static::STATUS_DELIVERED],
                'reviewKey'
            );
        }

        return $handlers;
    }
}
