<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Payment\Transaction;

/**
 * XPayments client decoration
 *
 * @Extender\Mixin
 */
class XPaymentsClient extends \Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient
{

    /**
     * Checks if Save Card checkbox must be forced to be Required
     *
     * @param Transaction $transaction Payment transaction
     *
     * @return string
     */
    protected function getAllowSaveCard(Transaction $transaction)
    {
        $result = parent::getAllowSaveCard($transaction);

        if (
            \Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient::SAVE_CARD_DISABLED != $result
            && $transaction->getOrder()->hasSubscriptions()
        ) {
            $result = \Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient::SAVE_CARD_REQUIRED;
        }

        return $result;
    }
}
