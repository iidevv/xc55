<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\Logic\Export\Step;

use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice;
use CDev\Wholesale\Module\XC\ProductVariants\Model\Repo\ProductVariantWholesalePrice as ProductVariantWholesalePriceRepo;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
abstract class ProductVariantProducts extends \XLite\Logic\Export\Step\Products
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if ($this->generator->getOptions()->attrs !== 'none') {
            $columns[static::VARIANT_PREFIX . 'WholesalePrices'] = [static::COLUMN_MULTIPLE => true];
        }

        return $columns;
    }

   /**
     * Get column value for 'variantWholesalePrices' column
     *
     * @param array  $dataset Dataset
     * @param string $name    Column name
     * @param int    $i       Subcolumn index
     *
     * @return string
     */
    protected function getVariantWholesalePricesColumnValue(array $dataset, string $name, int $i)
    {
        $result = [];

        if (
            isset($dataset['variant'])
            && $dataset['variant']
        ) {
            $cnd = new CommonCell();
            $cnd->{ProductVariantWholesalePriceRepo::P_PRODUCT_VARIANT} = $dataset['variant'];

            $result = $this->convertWholesalePrices(
                Database::getRepo(ProductVariantWholesalePrice::class)->search($cnd)
            );
        }

        return $result;
    }
}
