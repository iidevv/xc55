<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\View;

use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariantPrice extends \CDev\Wholesale\View\ProductPrice
{
    /**
     * Define wholesale prices
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function defineWholesalePrices()
    {
        return $this->getProductVariant() && !$this->getProductVariant()->getDefaultPrice()
            ? Database::getRepo(ProductVariantWholesalePrice::class)->getWholesalePrices(
                $this->getProductVariant(),
                $this->getCart()->getProfile() ? $this->getCart()->getProfile()->getMembership() : null
            )
            : parent::defineWholesalePrices();
    }

    /**
     * @param $firstWholesalePrice
     * @return mixed
     */
    protected function getZeroTierDisplayPrice($firstWholesalePrice)
    {
        if (
            $this->getProductVariant()
            && $firstWholesalePrice->getOwner() instanceof \XLite\Model\Product
        ) {
            return $this->getProductVariant()->getDisplayPrice();
        }

        return parent::getZeroTierDisplayPrice($firstWholesalePrice);
    }

    /**
     * @return boolean
     */
    protected function isWholesalePricesEnabled()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->isWholesalePricesEnabled()
            : parent::isWholesalePricesEnabled();
    }
}
