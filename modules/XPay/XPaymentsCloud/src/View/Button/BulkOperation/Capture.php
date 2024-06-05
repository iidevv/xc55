<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button\BulkOperation;

/**
 * X-Payments bulk operation button widget
 */
class Capture extends ABulkOperation
{
    /**
     * Get operation
     *
     * @return string
     */
    protected function getOperation()
    {
        return \XPay\XPaymentsCloud\Model\BulkOperation::OPERATION_CAPTURE;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Capture selected';
    }
}
