<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\FormField\Select;

use Qualiteam\SkinActAftership\Model\AftershipCouriers;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Order;

/**
 * Select current shipping method couriers
 */
class ShippingMethodCouriers extends \XLite\View\FormField\Select\Regular
{
    /**
     * Default options
     */
    protected function getDefaultOptions(): array
    {
        $list = [];

        $list += $this->getShippingMethodCouriersList();

        return ['None' => static::t('SkinActAftership none')] + $list;
    }

    /**
     * Get order shipping methods
     */
    protected function getOrderShippingMethods(): ?array
    {
        $orderNumber = Request::getInstance()->order_number;

        return $orderNumber
            ? Database::getRepo(Order::class)->findOrderShippingMethodCouriers($orderNumber)
            : [];
    }

    /**
     * Get shipping method couriers in DB
     *
     * @return array|object[]
     */
    protected function getShippingMethodCouriers(): array
    {
        $orderShippingMethodCouriers = $this->getOrderShippingMethods();
        if ($orderShippingMethodCouriers) {
            $orderShippingMethodCouriers = explode(',', $orderShippingMethodCouriers['aftership_couriers']);

            return Database::getRepo(AftershipCouriers::class)
                ->findCouriers($orderShippingMethodCouriers);
        }

        return [];
    }

    /**
     * Get shipping method couriers list
     *
     * @return array
     */
    protected function getShippingMethodCouriersList(): array
    {
        $values = $this->getShippingMethodCouriers();
        $result = [];

        /** @var AftershipCouriers $value */
        foreach ($values as $value) {
            $result[$value->getName()] = $value->getName();
        }

        return $result;
    }
}