<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Notifications;

use XLite\Model\Order;
use XLite\Model\Product;

class DataPreProcessor
{
    /**
     * @param string $dir
     * @param array  $data
     *
     * @return array
     */
    public static function prepareDataForNotification($dir, array $data)
    {
        switch ($dir) {
            case 'low_limit_warning':
                $data = static::prepareLowLimitWarningData($data);
                break;
            case 'order_tracking_information':
                $data = static::prepareOrderTrackingInformationData($data);
                break;
            case 'failed_transaction':
                $data = static::prepareFailedTransactionData($data);
                break;
            case 'backorder_created':
                $data = static::prepareBackorderCreatedData($data);
                break;
            case 'access_link':
                $data = static::prepareAccessLinkData($data);
                break;
        }

        return $data;
    }

    protected static function prepareAccessLinkData(array $data): array
    {
        return [
            'profile' => $data['profile'] ?? null,
            'acc'     => $data['access control cell'] ?? null
        ];
    }

    protected static function prepareBackorderCreatedData(array $data): array
    {
        $result = [ 'order' => null ];
        if (
            !empty($data['order'])
            && $data['order'] instanceof Order
        ) {
            $result['order'] = $data['order'];
        }

        return $result;
    }

    protected static function prepareLowLimitWarningData(array $data): array
    {
        $result = [ 'product' => null ];
        if (
            !empty($data['product'])
            && $data['product'] instanceof Product
        ) {
            $result['product'] = $data['product']->prepareDataForNotification();
        }

        return $result;
    }

    protected static function prepareOrderTrackingInformationData(array $data): array
    {
        $result = [];

        if (
            !empty($data['order'])
            && $data['order'] instanceof Order
        ) {
            $result['order'] = $data['order'];
        }

        return $result;
    }

    protected static function prepareFailedTransactionData(array $data): array
    {
        $result = [
            'transaction' => null
        ];

        /* @var Order $order */
        if (
            !empty($data['order'])
            && ($order = $data['order']) instanceof Order
            && ($lastTransaction = $order->getPaymentTransactions()->last())
        ) {
            $result['transaction'] = $lastTransaction;
        }

        return $result;
    }
}
