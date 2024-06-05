<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select;

use XLite\View\FormField\Select\ASelect;

class SyncStatuses extends ASelect
{
    const STATUS_ANY     = '';
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';

    const STATUSES = [
        self::STATUS_ANY     => 'Any (success and/or error)',
        self::STATUS_SUCCESS => 'Success',
        self::STATUS_ERROR   => 'Error',
    ];

    /**
     * @inheritDoc
     */
    protected function getDefaultOptions()
    {
        return self::STATUSES;
    }
}
