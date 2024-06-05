<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * Order payment status
 * @Extender\Mixin
 */
abstract class Payment extends \XLite\Model\Order\Status\Payment
{
    /**
     * Return status handlers list
     *
     * @return array
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

        $notPaidStatuses = [
            static::STATUS_QUEUED,
            static::STATUS_REFUNDED,
            static::STATUS_PART_PAID,
            static::STATUS_DECLINED,
            static::STATUS_CANCELED,
            static::STATUS_AUTHORIZED,
        ];

        foreach ($notPaidStatuses as $status) {
            if (!isset($handlers[$status])) {
                $handlers[$status] = [
                    static::STATUS_PAID => [],
                ];
            }
            array_unshift(
                $handlers[$status][static::STATUS_PAID],
                'reviewKey'
            );
        }

        return $handlers;
    }
}
