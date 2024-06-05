<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select\StatusesMapping\Inline;

use XLite\View\FormField\Select\ASelect;

class SkuvaultSaleStatuses extends ASelect
{
    protected function getDefaultOptions()
    {
        $statuses = [
            '',
            'Pending',
            'ReadyToShip',
            'Cancelled',
            'Completed',
            'ShippedUnpaid',
        ];

        return array_combine($statuses, $statuses);
    }
}
