<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Core\Wallets as XPaymentsWallets;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Decorated Cart controller
 *
 * @Extender\Mixin
 */
abstract class Cart extends \XLite\Controller\Customer\Cart implements \XLite\Base\IDecorator
{
    /**
     * Data for event to be passed between functions
     *
     * @var array
     */
    protected $xpaymentsBuyWithEvent;

    /**
     * If item was added successfully:
     * - create virtual cart with only that product (and remove product from main cart as well)
     * - initiate Buy With Wallet
     * - do not add successful top message
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return void
     */
    protected function processAddItemSuccess($item)
    {
        parent::processAddItemSuccess($item);

        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithWallet) {
            // Check if passed value is valid and actual method is available
            $walletMethod = XPaymentsHelper::getWalletMethod(\XLite\Core\Request::getInstance()->xpaymentsWalletId);
            if ($walletMethod) {
                $walletId = XPaymentsHelper::getMethodWalletId($walletMethod);
                $walletCart = $this->assignBuyWithWalletItem($walletId, $item);

                // Remove item from real cart
                $existingItem = $this->getCart()->getItemByItem($item);
                if ($existingItem->getAmount() > $item->getAmount()) {
                    $existingItem->setAmount($existingItem->getAmount() - $item->getAmount());
                } else {
                    $this->getCart()->getItems()->removeElement($existingItem);
                }

                $this->xpaymentsBuyWithEvent = [
                    'walletId' => $walletId,
                    'total' => $walletCart->getTotal(),
                    'currency' => $walletCart->getCurrency()->getCode(),
                    'shippingMethods' => $walletMethod->getProcessor()->getWalletShippingMethodsList($walletCart),
                    'requiredShippingFields' => $walletMethod->getProcessor()->getWalletRequiredAddressFields('shipping', $walletCart),
                    'requiredBillingFields' => $walletMethod->getProcessor()->getWalletRequiredAddressFields('billing', $walletCart),
                ];

            }
        }
    }

    /**
     * Add product to cart
     *
     * @return void
     */
    protected function doActionAdd()
    {
        $this->xpaymentsBuyWithEvent = null;

        parent::doActionAdd();

        if ($this->xpaymentsBuyWithEvent) {
            \XLite\Core\TopMessage::getInstance()->unloadPreviousMessages();
            \XLite\Core\Event::getInstance()->clear();
            \XLite\Core\Event::xpaymentsBuyWithWalletReady($this->xpaymentsBuyWithEvent);
        }
    }

    /**
     * Moves order item to cleared Buy With Wallet Cart
     *
     * @param string $walletId
     * @param \XLite\Model\OrderItem $orderItem
     *
     * @return \XLite\Model\Cart
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assignBuyWithWalletItem($walletId, \XLite\Model\OrderItem $orderItem)
    {
        $walletCart = XPaymentsWallets::getBuyWithWalletCart($walletId);
        $walletCart->clear();

        if (!$walletCart->isPersistent()) {
            \XLite\Core\Database::getEM()->persist($walletCart);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\Session::getInstance()->buy_with_wallet_order_id = $walletCart->getOrderId();
        }

        // We do not use addItem wrapper here, because it makes unwanted amount checks
        $walletCart->addItems($orderItem);
        $orderItem->setOrder($walletCart);

        $walletCart->calculate();

        return $walletCart;
    }

    /**
     * Disable redirect to cart after 'Add-to-cart' action
     *
     * @return void
     */
    protected function setURLToReturn()
    {
        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithWallet) {
            // Skip setting redirect URL
        } else {
            parent::setURLToReturn();
        }
    }

}
