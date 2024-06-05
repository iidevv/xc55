<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model\Payment;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class BackendTransaction extends \XLite\Model\Payment\BackendTransaction
{
    public const TRAN_TYPE_SC_TRANSFER         = 'scTransfer';
    public const TRAN_TYPE_SC_TRANSFER_REVERSE = 'scTransferReverse';
}
