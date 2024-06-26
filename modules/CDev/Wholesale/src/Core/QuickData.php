<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class QuickData extends \XLite\Core\QuickData
{
    protected $updatingWholesalePrices = false;

    /**
     * Update product quick data
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public function updateProductDataInternal(\XLite\Model\Product $product)
    {
        $origMembership = $product->getWholesaleMembership();
        $this->updatingWholesalePrices = true;

        parent::updateProductDataInternal($product);

        $product->setWholesaleMembership($origMembership);
        $this->updatingWholesalePrices = false;
    }

    /**
     * Get memberships
     *
     * @param \XLite\Model\Product $product    Product
     * @param mixed                $membership Membership
     *
     * @return array
     */
    public function updateData(\XLite\Model\Product $product, $membership)
    {
        if ($this->updatingWholesalePrices) {
            $product->setWholesaleMembership($membership);
        }

        return parent::updateData($product, $membership);
    }
}
