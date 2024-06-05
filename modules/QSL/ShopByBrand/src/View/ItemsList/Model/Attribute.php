<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Attributes items list
 * @Extender\Mixin
 */
class Attribute extends \XLite\View\ItemsList\Model\Attribute
{
    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return bool
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $isBrand = $this->isBrandAttribute($entity);
        $result  = parent::removeEntity($entity);
        if ($isBrand) {
            $this->resetBrandAttributeSetting();
        }

        return $result;
    }

    /**
     * Get ID of the attribute used to store product brands.
     *
     * @param \XLite\Model\AEntity $attribute Attribute entity
     *
     * @return int
     */
    protected function isBrandAttribute(\XLite\Model\AEntity $attribute)
    {
        return ($attribute instanceof \XLite\Model\Attribute) && $attribute->isBrandAttribute();
    }

    /**
     * Discards the brand attribute setting.
     */
    protected function resetBrandAttributeSetting()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            [
                'category' => 'QSL\ShopByBrand',
                'name'     => 'shop_by_brand_field_id',
                'value'    => 0,
            ]
        );
    }
}
