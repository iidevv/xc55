<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Module\XC\ProductVariants\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\Model\ProductVariant
{
    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    public function isShowStockWarning()
    {
        return $this->getProduct() && $this->getProduct()->isUpcomingProduct()
            ? false
            : parent::isShowStockWarning();
    }
}
