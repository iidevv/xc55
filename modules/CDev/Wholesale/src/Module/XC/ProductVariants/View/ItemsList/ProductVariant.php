<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\View\ItemsList;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\View\ItemsList\Model\ProductVariant
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        foreach ($columns as $k => $v) {
            if ($k == 'price') {
                $columns['wholesalePrices'] =  [
                    static::COLUMN_CLASS   => 'CDev\Wholesale\Module\XC\ProductVariants\View\FormField\WholesalePrices',
                    static::COLUMN_ORDERBY => $v[static::COLUMN_ORDERBY] + 1,
                ];
                break;
            }
        }

        return $columns;
    }

    /**
     * @return array
     */
    protected function getColspanHeaders()
    {
        return ['price' => ['wholesalePrices']];
    }
}
