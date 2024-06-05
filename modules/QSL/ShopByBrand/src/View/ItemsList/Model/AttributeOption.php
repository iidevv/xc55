<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * List of attribute options.
 * @Extender\Mixin
 */
class AttributeOption extends \XLite\View\ItemsList\Model\AttributeOption
{
    /**
     * Insert new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     */
    protected function insertNewEntity(\XLite\Model\AEntity $entity)
    {
        parent::insertNewEntity($entity);

        if ($entity->isBrandAttribute()) {
            $entity->createAssociatedBrand();
        }
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return bool
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        if ($entity->isBrandAttribute()) {
            $entity->deleteAssociatedBrand();
        }

        return parent::removeEntity($entity);
    }
}
