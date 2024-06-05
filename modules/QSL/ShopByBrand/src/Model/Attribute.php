<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated attribute model.
 * @Extender\Mixin
 */
class Attribute extends \XLite\Model\Attribute
{
    protected static $checkedAssociatedBrands = [];

    /**
     * Check if the attribute is the one that stores product brands.
     *
     * @return bool
     */
    public function isBrandAttribute()
    {
        $brandAttrId = \XLite\Core\Database::getRepo(\XLite\Model\Attribute::class)
            ->getBrandAttributeId();

        return $this->getId() === $brandAttrId;
    }

    /**
     * Set attribute value
     *
     * @param \XLite\Model\Product $product Product
     * @param mixed                $data    Value
     */
    public function setAttributeValue(\XLite\Model\Product $product, $data)
    {
        parent::setAttributeValue($product, $data);

        if (
            $this->isBrandAttribute()
            && isset($data['value'][0])
            && empty(static::$checkedAssociatedBrands[$data['value'][0]]) // don't check/process the same option more than once
        ) {
            // Make $this->getAttributeOptions() return all options including the one that has just been added
            \XLite\Core\Database::getEM()->flush();

            $brandRepo = \XLite\Core\Database::getRepo(\QSL\ShopByBrand\Model\Brand::class);

            // Update associated brands
            foreach ($this->queryFreshAttributeOptions() as $option) {
                $brand = $brandRepo->findOneByOption($option);

                if (!$brand) {
                    $option->createAssociatedBrand();
                }

                static::$checkedAssociatedBrands[$option->getName()] = true;
            }
        }
    }

    protected function queryFreshAttributeOptions()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\AttributeOption::SEARCH_ATTRIBUTE} = $this;

        return \XLite\Core\Database::getRepo(\XLite\Model\AttributeOption::class)
            ->search($cnd);
    }
}
