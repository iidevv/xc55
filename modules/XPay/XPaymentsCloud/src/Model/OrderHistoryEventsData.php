<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Order history event data
 *
 * @Extender\Mixin
 */
abstract class OrderHistoryEventsData extends \XLite\Model\OrderHistoryEventsData implements \XLite\Base\IDecorator
{
    use ClearCCDataTrait;
}
