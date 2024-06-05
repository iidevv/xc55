<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Core;

use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Wallets helper class
 */
class Wallets extends \XLite\Base\Singleton
{
    /**
     * X-Payments Buy With Wallet cart
     *
     * @var \XLite\Model\Cart
     */
    protected static $buyWithWalletCart = null;

    /**
     * Returns true if "Checkout with" for any wallet is enabled
     *
     * @return boolean
     */
    public static function isCheckoutWithAnyWalletEnabled()
    {
        $anyMethodApplicable = false;
        $wallets = array_keys(XPaymentsHelper::getWalletServiceNames());

        foreach ($wallets as $walletId) {
            $anyMethodApplicable = $anyMethodApplicable || static::isCheckoutWithWalletEnabled($walletId);
            if ($anyMethodApplicable) {
                break;
            }
        }

        return $anyMethodApplicable;
    }

    /**
     * Returns true if "Checkout with" for specified wallet is enabled
     *
     * @param string $walletId Wallet ID
     * @param \XLite\Model\Cart $cart Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isCheckoutWithWalletEnabled($walletId, \XLite\Model\Cart $cart = null)
    {
        static $result;

        $index = $walletId . (null !== $cart ? '-cart' : '');

        if (!isset($result[$index])) {
            $paymentMethod = XPaymentsHelper::getWalletMethod($walletId);

            $result[$index] =
                $paymentMethod
                && $paymentMethod->isEnabled()
                && ('applePay' !== $walletId || static::isBrowserMaySupportApplePay());

            if ($cart && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($cart, $paymentMethod);
            }

        }

        return $result[$index];
    }

    /**
     * Checks cart and return it only if it is not empty
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return \XLite\Model\Cart|null
     */
    public static function getNotEmptyCart(\XLite\Model\Cart $cart)
    {
        if (
            $cart
            && $cart::ORDER_ZERO < $cart->getTotal()
            && $cart->checkCart()
        ) {
            $result = $cart;
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Check by user agent if browser can support Apple Pay at all
     *
     * @return bool
     */
    public static function isBrowserMaySupportApplePay()
    {
        $ua = \XLite\Core\Request::getInstance()->getClientUserAgent();

        return (
            false !== strpos($ua, 'Safari')
            && false === strpos($ua, 'Chrome')
            && (
                false !== strpos($ua, 'iPhone')
                || false !== strpos($ua, 'iPad')
                || false !== strpos($ua, 'Macintosh')
            )
        );
    }

    /**
     * Returns Buy With Wallet Cart (or creates it)
     *
     * @param string walletId Wallet ID used for Checkout With Wallet feature
     * @param bool $doCalculate
     *
     * @return \XLite\Model\Cart
     */
    public static function getBuyWithWalletCart($walletId, $doCalculate = true)
    {
        if (is_null(static::$buyWithWalletCart)) {
            $orderId = \XLite\Core\Session::getInstance()->buy_with_wallet_order_id;
            if ($orderId) {
                $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->findOneForCustomer($orderId);
                if (
                    $cart
                    && (!$cart->hasCartStatus() || !$cart->isBuyWithWallet($walletId))
                ) {
                    unset(\XLite\Core\Session::getInstance()->buy_with_wallet_order_id, $cart);
                }
            }

            if (!isset($cart)) {
                // Cart not found - create a new instance
                $cart = new \XLite\Model\Cart;
                $cart->markAsBuyWithWallet($walletId);
                $cart->initializeCart();
            }

            static::$buyWithWalletCart = $cart;

            if ($doCalculate) {
                $auth = \XLite\Core\Auth::getInstance();
                if ($auth->isLogged()
                    && (
                        !$cart->getProfile()
                        || $auth->getProfile()->getProfileId() != $cart->getProfile()->getProfileId()
                    )
                ) {
                    $cart->setProfile($auth->getProfile());
                    $cart->setOrigProfile($auth->getProfile());
                }

                if ($cart->isPersistent()) {
                    $cart->renew();
                    \XLite\Core\Session::getInstance()->buy_with_wallet_order_id = $cart->getOrderId();
                }
            }
        }

        return static::$buyWithWalletCart;
    }

}
