<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Controller\Admin;


use XLite\Core\Database;
use XLite\Core\Event;
use XLite\Core\OrderHistory;
use XLite\Core\TopMessage;
use XLite\Model\Profile;

class CreateOrder extends \XLite\Controller\Admin\AAdmin
{

    protected function doActionCreateOrder()
    {
        $order = new \XLite\Model\Order;

        $order->setShippingStatus(\XLite\Model\Order\Status\Shipping::STATUS_NEW);
        $order->setPaymentStatus(\XLite\Model\Order\Status\Payment::STATUS_QUEUED);

        $currency = \XLite::getInstance()->getCurrency();
        $order->setCurrency($currency);

        $profile = new Profile();

        $address = new \XLite\Model\Address;
        $address->setIsBilling(true);
        $address->setIsShipping(true);
        $address->setProfile($profile);

        Database::getEM()->persist($address);

        $profile->addAddresses($address);

        $profile->setOrder($order);

        Database::getEM()->persist($profile);

        $order->setProfile($profile);
        $order->setOrigProfile(null);

        $order->setManuallyCreated(true);

        $order->setOrderNumber(
            \XLite\Core\Database::getRepo('XLite\Model\Order')->findNextOrderNumber()
        );

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(['service_name' => 'PhoneOrdering']);

        $transaction = new \XLite\Model\Payment\Transaction();

        $order->addPaymentTransactions($transaction);
        $transaction->setOrder($order);

        $transaction->setPaymentMethod($method);

        \XLite\Core\Database::getEM()->persist($method);

        $transaction->setCurrency($order->getCurrency());

        $transaction->setStatus($transaction::STATUS_INITIALIZED);
        $transaction->setValue(0);
        $transaction->setType($method->getProcessor()->getInitialTransactionType($method));

        \XLite\Core\Database::getEM()->persist($transaction);

        $order->renewPaymentMethod();

        Database::getEM()->persist($order);
        Database::getEM()->flush();

        // ocm = order manually created
        OrderHistory::getInstance()
            ->registerEvent($order->getOrderId(), 'ocm', static::t('SkinActCreateOrder order created'));

        TopMessage::addInfo(static::t('SkinActCreateOrder order created'));

        Event::redirect(['url' => $this->buildURL('order', '', ['order_number' => $order->getOrderNumber()])]);
    }
}