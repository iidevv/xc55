<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Logic\ClearCCData\Step;

use XLite\Core\Database;
use XLite\Model\Payment\TransactionData as Model;

/**
 * TransactionData
 */
class TransactionData extends AStep
{
    /**
     * @inheritdoc
     */
    protected function getRepository()
    {
        return Database::getRepo(Model::class);
    }

}
