<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActInvoiceToQuote\View\FormField\Select;

use XLite\Core\Database;
use XLite\Model\Order\Status\Payment;

/**
 * Class payment method
 */
class OrderPaymentStatuses extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        $statusesDb = Database::getRepo(Payment::class)
            ->findAll();

        $statuses = [
            0 => static::t('SkinActInvoiceToQuote order payment status not selected')
        ];

        if ($statusesDb) {
            foreach ($statusesDb as $db) {
                $statuses[$db->getId()] = $db->getName();
            }
        }

        return $statuses;
    }
}