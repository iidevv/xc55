<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Logic\ClearCCData;

use XLite\Logic\AGenerator;
use XPay\XPaymentsCloud\Logic\ClearCCData\Step\OrderHistoryEventsData;
use XPay\XPaymentsCloud\Logic\ClearCCData\Step\TransactionData;

/**
 * Clear credit cards data generator
 */
class Generator extends AGenerator
{
    /**
     * @inheritdoc
     */
    protected function defineSteps()
    {
        return [
            OrderHistoryEventsData::class,
            TransactionData::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getEventName()
    {
        return 'clearCreditCardsData';
    }

}
