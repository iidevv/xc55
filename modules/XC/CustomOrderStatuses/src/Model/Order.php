<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Class represents an order
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Return base part of the certain "change status" handler name
     *
     * @param mixed  $oldStatus  Old order status
     * @param mixed  $newStatus  New order status
     * @param string $type Type
     *
     * @return string|array
     */
    protected function getStatusHandlers($oldStatus, $newStatus, $type)
    {
        $result = parent::getStatusHandlers($oldStatus, $newStatus, $type);

        $oldCode = $oldStatus->getCode();
        $newCode = $newStatus->getCode();

        if (!$oldCode || !$newCode) {
            $class = '\XLite\Model\Order\Status\\' . ucfirst($type);

            $oldCode = $oldCode ?: $class::STATUS_CUSTOM;
            $newCode = $newCode ?: $class::STATUS_CUSTOM;

            $statusHandlers = $class::getStatusHandlers();

            if (isset($statusHandlers[$oldCode]) && isset($statusHandlers[$oldCode][$newCode])) {
                $result = array_merge(
                    $result,
                    is_array($statusHandlers[$oldCode][$newCode])
                        ? array_unique($statusHandlers[$oldCode][$newCode])
                        : [$statusHandlers[$oldCode][$newCode]]
                );
            }
        }

        return $result;
    }
}
