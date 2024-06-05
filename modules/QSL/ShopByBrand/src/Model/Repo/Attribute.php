<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Model\Attribute as AttributeModel;

/**
 * Decorated repository class for the Attribute model.
 * @Extender\Mixin
 */
class Attribute extends \XLite\Model\Repo\Attribute
{
    /**
     * Get the attribute used to store product brands.
     *
     * @return \XLite\Model\Attribute
     */
    public function findBrandAttribute()
    {
        return $this->find($this->getBrandAttributeId());
    }

    /**
     * Get ID of the attribute used to store product brands.
     *
     * @return int
     */
    public function getBrandAttributeId()
    {
        return (int) Config::getInstance()->QSL->ShopByBrand->shop_by_brand_field_id;
    }

    /**
     * Delete entity
     *
     * @param \XLite\Model\AEntity $entity Entity to delete
     * @param bool                 $flush  Flag OPTIONAL
     */
    public function delete(\XLite\Model\AEntity $entity, $flush = self::FLUSH_BY_DEFAULT)
    {
        if (($entity instanceof AttributeModel) && ($entity->getId() === $this->getBrandAttributeId())) {
            // The user has deleted the whole Brands attribute, so we must
            // cascade it to all brand records otherwise orphaned records
            // will stay in the database
            $repo = Database::getRepo('QSL\ShopByBrand\Model\Brand');
            $ids = $repo->search(null, \XLite\Model\Repo\ARepo::SEARCH_MODE_IDS);
            $ids = is_array($ids) ? array_unique(array_filter($ids)) : [];
            //clearall not enough cuz we should trigger preremove too
            $repo->deleteInBatchById(array_flip($ids), false);

            // Also, we should drop the module setting
            $setting = Database::getRepo('XLite\Model\Config')->findOneByName('shop_by_brand_field_id');
            $setting->setValue(0);

            \XLite\Core\Database::getEM()->flush();
        }

        parent::delete($entity, $flush);
    }
}
