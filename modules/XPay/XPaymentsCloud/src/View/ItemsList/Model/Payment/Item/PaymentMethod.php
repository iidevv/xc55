<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\ItemsList\Model\Payment\Item;

use XCart\Extender\Mapping\Extender;

/**
 * Payment method
 *
 * @Extender\Mixin
 */
abstract class PaymentMethod extends \XLite\View\ItemsList\Model\Payment\Item\PaymentMethod implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XPay/XPaymentsCloud/items_list/payment_method.css';

        return $list;
    }

    /**
     * Get admin icon URL
     *
     * @return string
     */
    public function getAdminIconURL()
    {
        $result = parent::getAdminIconURL();

        $method = $this->getPayment();

        if ($method->isLegacyXpaymentsMethod()) {
            $result = 'https://my.x-cart.com/storage/addons/XPay/XPaymentsCloud/' . $method->getServiceName() . '.png';
        }

        return $result;
    }
}
