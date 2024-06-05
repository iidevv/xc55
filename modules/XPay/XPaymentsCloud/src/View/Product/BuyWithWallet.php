<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Product;

/**
 * Buy with Wallet widget
 */
class BuyWithWallet extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Widget parameter names
     */
    const PARAM_WALLET_CLASS = 'walletClass';
    const PARAM_WALLET_ID = 'walletId';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_WALLET_ID => new \XLite\Model\WidgetParam\TypeString('Wallet ID', '', true),
            self::PARAM_WALLET_CLASS => new \XLite\Model\WidgetParam\TypeString('Wallet class', '', true),
        );
    }

    /**
     * Is widget visible
     *
     * @return bool
     */
    protected function isVisible()
    {
        return
            parent::isVisible()
            && \XPay\XPaymentsCloud\Core\Wallets::isCheckoutWithWalletEnabled($this->getParam(self::PARAM_WALLET_ID));
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-buy-apple-pay-button';
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getWalletButtonClass()
    {
        return '\\XPay\\XPaymentsCloud\\View\\Button\\Product\\' . $this->getParam(self::PARAM_WALLET_CLASS);
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/product/details/buy_with_wallet_widget.twig';
    }
}
