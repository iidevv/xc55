<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\DataMappers\Order;

use XLite\Model\Order;
use CDev\GoogleAnalytics\Core\GA\DataMappers\AMapper;
use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\ICommon;
use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\IOrder;

class UAGA extends AMapper implements IOrder
{
    public function getPurchaseData(Order $order): array
    {
        return $this->getInstance()->getPurchaseData($order);
    }

    public function getChangeData(Order $order, array $change): array
    {
        return $this->getInstance()->getChangeData($order, $change);
    }

    public function getPurchaseDataForBackend(Order $order, array $products = []): array
    {
        return $this->getInstance()->getPurchaseDataForBackend($order, $products);
    }

    public function getChangeDataForBackend(Order $order, array $change = [], array $products = []): array
    {
        return $this->getInstance()->getChangeDataForBackend($order, $change, $products);
    }

    /**
     * @return ICommon|IOrder
     */
    protected function getInstance(): ICommon
    {
        return $this->instance;
    }
}
