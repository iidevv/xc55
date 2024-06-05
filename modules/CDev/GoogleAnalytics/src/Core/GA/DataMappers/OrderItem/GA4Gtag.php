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

class GA4Gtag extends AMapper implements IOrderItem
{
    protected static function keys(): array
    {
        return \CDev\GoogleAnalytics\Core\GA\DataMappers\Product\GA4Gtag::keys();
    }

    public function getDataForBackend(OrderItem $item, $qty = null, int $index = 1): array
    {
        $data = $this->getData($item);

        if ($qty) {
            $data['quantity'] = $qty;
        }

        return $data;
    }

    public function getData(OrderItem $item): array
    {
        return static::map($this->getInstance()->getData($item));
    }

    /**
     * @return ICommon|IOrderItem
     */
    protected function getInstance(): ICommon
    {
        return $this->instance;
    }
}
