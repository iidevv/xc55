<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers;

use XLite\Model\OrderItem;

interface IOrderItem extends ICommon
{
    public function getData(OrderItem $item): array;

    /**
     * @param OrderItem $item
     * @param null      $qty
     * @param int       $index
     *
     * @return array
     */
    public function getDataForBackend(OrderItem $item, $qty = null, int $index = 1): array;
}
