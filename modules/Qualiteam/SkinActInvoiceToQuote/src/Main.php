<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActInvoiceToQuote;

use XLite\Core\Config;
use XLite\Module\AModule;

/**
 * Class main
 */
class Main extends AModule
{
    /**
     * Get config payment status id
     *
     * @return int
     */
    public function getConfigPaymentStatusId(): int
    {
        return (int) Config::getInstance()->Qualiteam->SkinActInvoiceToQuote->invoice_payment_method_id;
    }

    /**
     * Module has a payment status param
     *
     * @return bool
     */
    protected function hasConfigPaymentStatusId(): bool
    {
        return (bool) $this->getConfigPaymentStatusId();
    }

    /**
     * Show custom label
     *
     * @param $order
     *
     * @return bool
     */
    public static function isShowCustomLabel($order): bool
    {
        return (new Main)->hasConfigPaymentStatusId()
            && (new Main)->isOrderPaymentStatusIdEqualConfigPaymentStatusId($order);
    }

    /**
     * Is order payment status id eq config payment status
     *
     * @param $order
     *
     * @return bool
     */
    protected function isOrderPaymentStatusIdEqualConfigPaymentStatusId($order): bool
    {
        return $order
            && $order->getPaymentStatus()
            && $order->getPaymentStatus()->getId() === $this->getConfigPaymentStatusId();
    }
}