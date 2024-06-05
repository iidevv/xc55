<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Product;

/**
 * Buy with wallet price update helper widget
 */
class BuyWithWalletPriceUpdate extends \XLite\View\Product\Details\Customer\Widget
{

    /**
     * Is widget visible
     *
     * @return bool
     */
    protected function isVisible()
    {
        return
            parent::isVisible()
            && \XPay\XPaymentsCloud\Core\Wallets::isCheckoutWithAnyWalletEnabled();
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-buy-with-wallet-price-update';
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/product/details/buy_with_wallet_price_update.twig';
    }

}