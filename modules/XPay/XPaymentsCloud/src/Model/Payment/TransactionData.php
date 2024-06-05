<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Payment;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Model\ClearCCDataTrait;

/**
 * Transaction data storage
 *
 * @Extender\Mixin
 */
abstract class TransactionData extends \XLite\Model\Payment\TransactionData implements \XLite\Base\IDecorator
{
    use ClearCCDataTrait;
}
