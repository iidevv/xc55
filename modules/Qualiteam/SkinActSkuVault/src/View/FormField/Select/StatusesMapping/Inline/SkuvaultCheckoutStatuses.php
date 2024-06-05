<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select\StatusesMapping\Inline;

use XLite\View\FormField\Select\ASelect;

class SkuvaultCheckoutStatuses extends ASelect
{
    const STATUSES = [
        '',
        'NotVisited',
        'Visited',
        'OnHold',
        'Completed',
        'CompletedOffline',
        'Cancelled',
    ];

    protected function getDefaultOptions()
    {
        $statuses = static::STATUSES;

        return array_combine($statuses, $statuses);
    }
}
