<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model\Repo\AttributeValue;

use XCart\Extender\Mapping\Extender;

/**
 * Attribute values repository
 * @Extender\Mixin
 */
class AttributeValueSelect extends \XLite\Model\Repo\AttributeValue\AttributeValueSelect
{
    /**
     * Finds the AttributeValueSelect model for the product and the attribute.
     *
     * @param \XLite\Model\Product   $product   Product that we are looking the value for.
     * @param \XLite\Model\Attribute $attribute Attribute that we are looking the value for.
     *
     * @return \QSL\ShopByBrand\Model\AttributeValueSelect
     */
    public function findProductAttributeValue(\XLite\Model\Product $product, \XLite\Model\Attribute $attribute)
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{static::SEARCH_PRODUCT}   = $product;
        $cnd->{static::SEARCH_ATTRIBUTE} = $attribute;

        $result = $this->search($cnd);

        return count($result) ? $result[0] : null;
    }
}
