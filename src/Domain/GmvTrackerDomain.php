<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Domain;

use JsonException;
use Symfony\Component\Filesystem\Filesystem;
use XLite\Model\Order;
use XLite\Model\Payment\Transaction;

final class GmvTrackerDomain implements GmvTrackerDomainInterface
{
    public const GMV_DATA_FILE_NAME = 'gmv_data.jsonl';

    private Filesystem $filesystem;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * @throws JsonException
     */
    public function saveOrderGmvData(array $orderGmvData): void
    {
        $filename = LC_DIR_GMV . static::GMV_DATA_FILE_NAME;
        $newLine  = json_encode($orderGmvData, JSON_THROW_ON_ERROR);

        $this->filesystem->appendToFile($filename, "$newLine\n");
    }

    public function prepareOrderGmvData(Order $order): array
    {
        /** @var Transaction $paymentTransaction */
        $paymentTransaction = $order->getPaymentTransactions()->last();

        $discountedSubtotal = array_reduce(
            $order->getItems()->toArray(),
            static function ($carry, $orderItem) {
                return ($carry + $orderItem->getDiscountedSubtotal());
            },
        );

        return [
            'orderId'       => $order->getOrderId(),
            'date'          => $order->getDate(),
            'currency'      => $order->getCurrency()->getCode(),
            'subtotal'      => $discountedSubtotal,
            'paymentMethod' => $paymentTransaction
                ? $paymentTransaction->getPaymentMethod()->getName()
                : '',
            'transactionId' => $paymentTransaction
                ? $paymentTransaction->getPublicTxnId()
                : '',
        ];
    }
}
