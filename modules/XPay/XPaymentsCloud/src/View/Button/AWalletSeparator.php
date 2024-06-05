<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button;

use \XPay\XPaymentsCloud\Core\Wallets as XPaymentsWallets;

/**
 * Checkout buttons separator
 */
abstract class AWalletSeparator extends \XLite\View\Button\ButtonsSeparator
{
    /**
     * Returns Wallet ID of wallet used for checkout
     *
     * @return string
     */
    abstract protected function getWalletId();

    /**
     * Checks if Checkout with wallet button is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $cart = $this->getNotEmptyCart();

        return
            parent::isVisible()
            && XPaymentsWallets::isCheckoutWithWalletEnabled($this->getWalletId(), $cart);
    }
    /**
     * Checks current cart and return it only if it is not empty
     *
     * @return \XLite\Model\Cart
     */
    protected function getNotEmptyCart()
    {
        return XPaymentsWallets::getNotEmptyCart($this->getCart());
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/button/buttons_separator.twig';
    }

}
