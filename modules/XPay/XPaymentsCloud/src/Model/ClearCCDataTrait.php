<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model;

use XPay\XPaymentsCloud\Core\ClearCCData;

/**
 * ClearCCDataTrait
 */
trait ClearCCDataTrait
{
    /**
     * Clear credit card data
     */
    public function clearCCData()
    {
        if ($this->isPersistent()) {
            ClearCCData::getInstance()->clearCCData($this);
        }
    }

}
