<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XC\AvaTax\Core\TaxCore;

/**
 * @package XC\AvaTax\Controller\Admin
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        $this->orderUpdatedCallback(
            $this->getOrderChanges(),
            $this->getOrder()
        );
    }

    /**
     * @param array                                     $diff
     * @param \XLite\Model\Order|\XC\AvaTax\Model\Order $order
     */
    protected function orderUpdatedCallback(array $diff, \XLite\Model\Order $order)
    {
        if ($diff && TaxCore::getInstance()->isValid() && $order->hasAvataxTaxes()) {
            TaxCore::getInstance()->adjustTransactionRequest($order, TaxCore::OTHER, print_r($diff, true));
        }
    }
}
