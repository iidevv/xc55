<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints;

use XLite\Model\Cart;
use XLite\Model\Order;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Profile;

class Params
{
    /**
     * @var \XLite\Model\Order
     */
    protected Order $order;

    /**
     * @var \XLite\Model\Payment\BackendTransaction
     */
    protected BackendTransaction $backendTransaction;

    /**
     * @return \XLite\Model\Cart
     */
    public function getCart(): Cart
    {
        return Cart::getInstance();
    }

    /**
     * @return \XLite\Model\Profile
     */
    public function getProfile(): Profile
    {
        return Cart::getInstance()->getProfile();
    }

    /**
     * @return \XLite\Model\Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return void
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return \XLite\Model\Payment\BackendTransaction
     */
    public function getTransaction(): BackendTransaction
    {
        return $this->backendTransaction;
    }

    /**
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     *
     * @return void
     */
    public function setTransaction(BackendTransaction $transaction): void
    {
        $this->backendTransaction = $transaction;
    }
}