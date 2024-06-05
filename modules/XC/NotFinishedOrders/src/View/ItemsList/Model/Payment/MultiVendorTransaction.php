<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View\ItemsList\Model\Payment;

use XCart\Extender\Mapping\Extender;

/**
 * Payment transactions items list
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class MultiVendorTransaction extends \XLite\View\ItemsList\Model\Payment\Transaction
{
    /**
     * Return orders for 'order' column
     *
     * @param \XLite\Model\Payment\Transaction $entity Entity
     *
     * @return \XLite\Model\Order[]
     */
    protected function getOrders($entity)
    {
        $nfo = $this->getLinkedOrder($entity);

        return $nfo ? [$nfo] : parent::getOrders($entity);
    }
}
