<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\ItemsList\Payment\Method\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract admin-based payment methods list
 *
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\ItemsList\Payment\Method\Admin\AAdmin implements  \XLite\Base\IDecorator
{
    /**
     * Check - method can remove or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    protected function canRemoveMethod(\XLite\Model\Payment\Method $method)
    {
        return parent::canRemoveMethod($method)
            && !$method->isXpaymentsWallet();
    }

    /**
     * Get line class
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    protected function getLineClass(\XLite\Model\Payment\Method $method)
    {
        $class = parent::getLineClass($method);

        if ($method->isXpaymentsWallet()) {
            $class .= ' xpayments-wallet-method';
        }

        return $class;
    }

    /**
     * Defines JS files for widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/items_list/payment/methods/controller.js';
        return $list;
    }

    /**
     * Defines CSS files for widget
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/items_list/payment/methods/style.css';
        return $list;
    }
}
