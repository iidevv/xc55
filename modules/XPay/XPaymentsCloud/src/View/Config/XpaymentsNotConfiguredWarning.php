<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Config;

use XPay\XPaymentsCloud\Main as XPaymentsHelper;

class XpaymentsNotConfiguredWarning extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/product/xpayments_not_configured_warning.twig';
    }

    /**
     * @return bool
     */
    protected function isXpaymentsSubscriptionsConfiguredAndActive()
    {
        return XPaymentsHelper::isXpaymentsSubscriptionsConfiguredAndActive();
    }

    /**
     * @return int
     */
    protected function getXpaymentsPaymentMethodId()
    {
        return XPaymentsHelper::getPaymentMethod()->getMethodId();
    }

}
