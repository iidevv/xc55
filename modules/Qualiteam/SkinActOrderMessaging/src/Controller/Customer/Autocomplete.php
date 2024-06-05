<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Controller\Customer;

use XLite\Core\Converter;
use XCart\Extender\Mapping\Extender;

/**
 * Autocomplete controller
 * @Extender\Mixin
 */
class Autocomplete extends \XLite\Controller\Customer\Autocomplete
{

    public const ORDERS_MAX_RESULTS = 100000;

    /**
     * Assemble dictionary - conversation recipient
     *
     * @param string $term Term
     *
     * @return array
     */
    protected function assembleDictionaryOrderMessagingOrders($term)
    {
        $orders = \XLite\Core\Database::getRepo('\XLite\Model\Order')
            ->findOrdersByTerm($term, static::ORDERS_MAX_RESULTS);

        return $this->packOrdersData($orders);
    }

    /**
     * Get certain data from profile array for new array
     *
     * @param array $profiles Array of profiles
     *
     * @return array
     */
    protected function packOrdersData(array $orders)
    {
        $result = [];

        if ($orders) {
            foreach ($orders as $k => $order) {
                $result[$order->getOrderId()] = $order->getOrderNumber() . ' - ' . Converter::getInstance()->formatTime($order->getDate());
            }
        }

        return $result;
    }
}