<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\Model\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping status
 *
 * @Extender\Mixin
 */
class Shipping extends \XLite\Model\Order\Status\Shipping
{
    public const STATUS_CUSTOM = 'CUSTOM';

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return \XLite\Model\Order\Status\Shipping
     */
    public function setName($name)
    {
        $this->setCustomerName($name);

        return parent::setName($name);
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
            self::STATUS_CUSTOM => [
                self::STATUS_SHIPPED => ['ship'],
            ],
        ]);
    }
}
