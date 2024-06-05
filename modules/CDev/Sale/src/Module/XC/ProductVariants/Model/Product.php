<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\XC\ProductVariants\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend({"CDev\Sale","XC\ProductVariants"})
 */
class Product extends \XLite\Model\Product
{
    protected $onSaleVariantsCount;

    protected function getOnSaleVariantsCount()
    {
        if (!isset($this->onSaleVariantsCount)) {
            $this->onSaleVariantsCount = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                ->getOnSaleVariantsCountByProduct($this);
        }

        return $this->onSaleVariantsCount;
    }

    /**
     * Check if product has sales
     *
     * @return bool
     */
    public function hasParticipateSale()
    {
        return parent::hasParticipateSale()
            || (
                $this->hasVariants()
                && 0 < $this->getOnSaleVariantsCount()
            );
    }
}
