<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace CDev\Paypal\Domain;

use CDev\Paypal\Main;
use XCart\Domain\GmvTrackerDomainInterface;
use XLite\Model\Order;
use XLite\Model\Payment\Transaction;

final class GmvTrackerDomainDecorator implements GmvTrackerDomainInterface
{
    private GmvTrackerDomainInterface $inner;

    public function __construct(
        GmvTrackerDomainInterface $inner
    ) {
        $this->inner = $inner;
    }

    public function saveOrderGmvData(array $orderGmvData): void
    {
        $this->inner->saveOrderGmvData($orderGmvData);
    }

    public function prepareOrderGmvData(Order $order): array
    {
        $result = $this->inner->prepareOrderGmvData($order);

        /** @var Transaction $paymentTransaction */
        $paymentTransaction = $order->getPaymentTransactions()->last();

        if ($paymentTransaction) {
            $paymentMethod = $paymentTransaction->getPaymentMethod();

            if ($paymentMethod->getServiceName() === Main::PP_METHOD_PCP) {
                $result['merchantId'] = $paymentMethod->getSetting('merchant_id');
            }
        }

        return $result;
    }
}
