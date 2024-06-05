<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Decorated product model.
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Cached brand model.
     *
     * @var Brand
     */
    protected $brand;

    /**
     * Get the name of the product brand.
     *
     * @return string
     */
    public function getBrandName()
    {
        return $this->hasBrand() ? $this->getBrand()->getName() : '';
    }

    /**
     * Check if the product has an associated brand.
     *
     * @return bool
     */
    public function hasBrand()
    {
        return !is_null($this->getBrand());
    }

    /**
     * Get the brand model for the product.
     *
     * @return Brand
     */
    public function getBrand()
    {
        if (!isset($this->brand)) {
            $this->brand = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')
                ->findProductBrand($this);
        }

        return $this->brand;
    }

    /**
     * Get position in the brand
     *
     * @param Brand $brand Brand entitny.
     *
     * @return int
     */
    public function getBrandPosition(Brand $brand)
    {
        $result = 0;
        if ($brand) {
            /** @var BrandProducts|null $brandProductsEntity */
            $brandProductsEntity = Database::getRepo('QSL\ShopByBrand\Model\BrandProducts')->findOneBy([
                'brand'   => $brand,
                'product' => $this
            ]);
            if ($brandProductsEntity) {
                $result = $brandProductsEntity->getOrderby();
            }
        }
        return $result;
    }
}
