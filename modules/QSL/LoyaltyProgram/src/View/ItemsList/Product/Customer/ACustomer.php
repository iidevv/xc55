<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * Decorated widget displaying a product cell.
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * Get CSS classes for a product cell.
     *
     * @param \XLite\Model\Product $product The product to look for
     *
     * @return string
     */
    public function getProductCellClass(\XLite\Model\Product $product)
    {
        $result = parent::getProductCellClass($product);

        $points = $product->hasDefinedRewardPoints()
            ? $product->getRewardPoints()
            : LoyaltyProgram::getInstance()->calculateEarnedRewardPoints($product->getDisplayPrice());

        if (0 < $points) {
            $result .= ' product-with-reward-points';
        }

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/LoyaltyProgram/promo/product_points.css';

        return $list;
    }
}
