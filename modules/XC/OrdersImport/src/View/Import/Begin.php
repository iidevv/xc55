<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrdersImport\View\Import;

use XCart\Extender\Mapping\Extender;

/**
 * Begin section
 * @Extender\Mixin
 */
class Begin extends \XLite\View\Import\Begin
{
    /**
     * Get options for selector 'Import mode'
     *
     * @return array
     */
    protected function getImportModeOptions()
    {
        return parent::getImportModeOptions() + [
            static::MODE_CREATE_ONLY => static::t('Create new items, but skip existing items') . ' (' . static::t('Orders') . ')',
        ];
    }
}
