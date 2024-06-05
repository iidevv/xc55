<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\DataMappers;

use XLite\Core\Config;
use XLite\Model\Base\Surcharge;
use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\IOrder;

class Order extends Common implements IOrder
{
    public function getPurchaseDataForBackend(\XLite\Model\Order $order, array $products = []): array
    {
        $data = $this->getPurchaseData($order);

        return $this->getCommonData($data, $products);
    }

    public function getPurchaseData(\XLite\Model\Order $order): array
    {
        $tax      = static::getTaxValue($order);
        $shipping = $order->getSurchargeSumByType(Surcharge::TYPE_SHIPPING);


        return [
            'id'          => $order->getOrderNumber(),
            'affiliation' => Config::getInstance()->Company->company_name,
            'revenue'     => $order->getTotal(),
            'tax'         => $tax,
            'shipping'    => $shipping,
            'coupon'      => '',
        ];
    }

    /**
     * Get tax value
     *
     * @param \XLite\Model\Order $order
     *
     * @return float
     */
    protected static function getTaxValue(\XLite\Model\Order $order)
    {
        $total = 0;

        /** @var \XLite\Model\Order\Surcharge $s */
        foreach ($order->getSurchargesByType(Surcharge::TYPE_TAX) as $s) {
            $total += $s->getValue();
        }

        return $total;
    }

    public function getChangeDataForBackend(\XLite\Model\Order $order, array $change = [], array $products = []): array
    {
        $data = $this->getChangeData($order, $change);

        return $this->getCommonData($data, $products);
    }

    public function getChangeData(\XLite\Model\Order $order, array $change): array
    {
        $data = $this->getPurchaseData($order);

        $change = array_merge(
            [
                'revenue'  => 0,
                'tax'      => 0,
                'shipping' => 0,
            ],
            array_filter($change)
        );

        return [
            'id'          => $data['id'],
            'affiliation' => $data['affiliation'],
            'revenue'     => $change['revenue'],
            'tax'         => $change['tax'],
            'shipping'    => $change['shipping'],
            'coupon'      => $data['coupon'],
        ];
    }

    protected function getCommonData(array $data, array $products): array
    {
        $result = [];

        $result['ti']  = $data['id'];
        $result['ta']  = $data['affiliation'];
        $result['tr']  = $data['revenue'];
        $result['tt']  = $data['tax'];
        $result['ts']  = $data['shipping'];
        $result['tcc'] = $data['coupon'];

        foreach ($products as $productData) {
            $result += $productData;
        }

        return $result;
    }
}
