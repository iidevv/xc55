<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\DataMappers\OrderItem;

use XLite\Model\OrderItem;
use CDev\GoogleAnalytics\Core\GA\DataMappers\AMapper;
use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\ICommon;
use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\IOrderItem;

class UAGA extends AMapper implements IOrderItem
{
    public function getData(OrderItem $item): array
    {
        return $this->getInstance()->getData($item);
    }

    public function getDataForBackend(OrderItem $item, $qty = null, int $index = 1): array
    {
        return $this->getInstance()->getDataForBackend($item, $qty, $index);
    }

    /**
     * @return ICommon|IOrderItem
     */
    protected function getInstance(): ICommon
    {
        return $this->instance;
    }
}
