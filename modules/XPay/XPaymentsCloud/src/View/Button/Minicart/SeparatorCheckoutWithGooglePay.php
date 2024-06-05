<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button\Minicart;

use XCart\Extender\Mapping\ListChild;
use \XPay\XPaymentsCloud\Core\Wallets as XPaymentsWallets;

/**
 * Minicart buttons separator
 *
 * @ListChild (list="minicart.horizontal.buttons", weight="79")
 */
class SeparatorCheckoutWithGooglePay extends \XPay\XPaymentsCloud\View\Button\AWalletSeparator
{
    /**
     * Returns Wallet ID of wallet used for checkout
     *
     * @return string
     */
    protected function getWalletId()
    {
        return 'googlePay';
    }

    /**
     * Checks if Checkout with Google Pay is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getNotEmptyCart();
    }
}
