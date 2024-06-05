<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select\StatusesMapping\Inline;

use XLite\View\FormField\Select\ASelect;

class SkuvaultPaymentStatuses extends ASelect
{
    protected function getDefaultOptions()
    {
        $statuses = [
            '',
            'NotSubmitted',
            'Submitted',
            'Deposited',
            'Cleared',
            'Failed',
        ];

        return array_combine($statuses, $statuses);
    }
}
