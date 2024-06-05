<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Module\QSL\ShopByBrand\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;
use XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\ShopByBrand")
 */
class Brand extends \QSL\ShopByBrand\View\ItemsList\Model\Brand
{
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['skip_sync_to_skuvault'] = [
            static::COLUMN_NAME    => static::t('Skip sync to SkuVault'),
            static::COLUMN_ORDERBY => 250,
            static::COLUMN_CLASS   => OnOff::class,
        ];

        return $columns;
    }
}
