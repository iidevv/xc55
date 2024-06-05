<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Logic\Import\Processor\AttributeValues;

use XCart\Extender\Mapping\Extender;

/**
 * Product attributes values import processor
 * @Extender\Mixin
 */
class AttributeValueSelect extends \XLite\Logic\Import\Processor\AttributeValues\AttributeValueSelect
{
    /**
     * Get attribute value data
     *
     * @param array                  $data      Import row data
     * @param \XLite\Model\Attribute $attribute Attribute object
     *
     * @return array
     */
    protected function getAttributeValueData($data, $attribute)
    {
        $newBrand = $attribute->isBrandAttribute()
            && !\XLite\Core\Database::getRepo('XLite\Model\AttributeOption')
                ->findOneByNameAndAttribute($data['value'], $attribute);

        $result = parent::getAttributeValueData($data, $attribute);

        if ($newBrand && isset($result['attribute_option'])) {
            // It is a new option, so we should associate a new brand entity with it
            $result['attribute_option']->createAssociatedBrand();
        }

        return $result;
    }
}
