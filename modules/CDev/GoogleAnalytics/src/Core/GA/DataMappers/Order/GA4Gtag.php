<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\DataMappers\Order;

use CDev\GoogleAnalytics\Model\Profile;
use XLite\Model\Order;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Core\GA\DataMappers\AMapper;
use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\IOrder;

class GA4Gtag extends AMapper implements IOrder
{
    protected static function keys(): array
    {
        return [
            'id'      => 'transaction_id',
            'revenue' => 'value',
        ];
    }

    public function getPurchaseDataForBackend(Order $order, array $products = []): array
    {
        $data = $this->getPurchaseData($order);

        $data['items'] = $products;

        return $data;
    }

    public function getPurchaseData(Order $order): array
    {
        return array_merge(
            static::addExtraFields($order),
            static::map($this->getInstance()->getPurchaseData($order))
        );
    }

    protected static function addExtraFields(?Order $order): array
    {
        $defaultCurrencyCode = GA::getResource()->getCurrencyCode();

        $currency = $order ? $order->getCurrency() : null;

        $result = [
            'currency' => $currency ? $currency->getCode() : $defaultCurrencyCode,
        ];

        if ($order && ($sessionId = self::getOrderSessionId($order))) {
            $result['session_id']  = $sessionId;
        }

        return $result;
    }

    protected static function getOrderSessionId(Order $order): ?string
    {
        /** @var Profile $profile */
        if ($profile = $order->getProfile()) {
            return $profile->getGaSessionId();
        }

        return null;
    }

    public function getChangeDataForBackend(Order $order, array $change = [], array $products = []): array
    {
        $data = $this->getChangeData($order, $change);

        $data['items'] = $products;

        return $data;
    }

    public function getChangeData(Order $order, array $change): array
    {
        return array_merge(
            static::addExtraFields($order),
            static::map($this->getInstance()->getChangeData($order, $change))
        );
    }

    /**
     * @return GA\Interfaces\DataMappers\ICommon|IOrder
     */
    protected function getInstance(): GA\Interfaces\DataMappers\ICommon
    {
        return $this->instance;
    }
}
