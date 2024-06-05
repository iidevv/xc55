<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Model;

/**
 * Settings dialog model widget
 */
class ModuleSettings extends \XLite\View\Model\ModuleSettings
{
    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     */
    protected function setModelProperties(array $data)
    {
        $newAttributeId = isset($data['shop_by_brand_field_id']) ? (int) $data['shop_by_brand_field_id'] : 0;
        $oldAttributeId = (int) $this->getModelObjectValue('shop_by_brand_field_id');

        $isAttributeChanged = ($newAttributeId !== $oldAttributeId);
        $isAttributeDeleted = !$newAttributeId;

        parent::setModelProperties($data);

        if ($isAttributeDeleted && $oldAttributeId) {
            $this->deleteBrands($oldAttributeId);
        } elseif ($isAttributeChanged && $newAttributeId) {
            $this->reattachBrands($oldAttributeId, $newAttributeId);
        }
    }

    /**
     * Delete all brands.
     *
     * @param int $oldAttributeId Brand attribute ID
     */
    protected function deleteBrands($oldAttributeId)
    {
        $repo = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand');
        $ids  = [];
        foreach ($repo->search() as $brand) {
            $ids[] = $brand->getBrandId();
        }

        foreach ($ids as $id) {
            $brand = $repo->find($id);
            $brand->delete();
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Reattach brands from options of the previous brand attribute to options of the new one.
     *
     * @param int $oldAttributeId ID of the previous brand attribute
     * @param int $newAttributeId ID of the new brand attribute
     */
    protected function reattachBrands($oldAttributeId, $newAttributeId)
    {
        $repo         = \XLite\Core\Database::getRepo('XLite\Model\Attribute');
        $newAttribute = $repo->find($newAttributeId);

        // Collect information on options of the new attribute

        $newAttributeOptions = [];
        foreach ($newAttribute->getAttributeOptions() as $option) {
            $newAttributeOptions[$option->getName()] = $option;
        }

        // Process existing brands and attach them to the new attribute

        foreach (\XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->search() as $record) {
            $brand = $record;
            if (isset($newAttributeOptions[$brand->getName()])) {
                // There is such an option, we just reassign the brand on the new one
                $brand->setOption($newAttributeOptions[$brand->getName()]);
                unset($newAttributeOptions[$brand->getName()]);
            } else {
                // New attribute doesn't have options with such a name, so we create a new one
                $option = new \XLite\Model\AttributeOption();
                $option->setAttribute($newAttribute);
                $option->setName($brand->getName());
                $option->getRepository()->insert($option);
                $brand->setOption($option);
            }
        }

        // Create brands for new options from the new attribute
        foreach ($newAttributeOptions as $option) {
            $option->createAssociatedBrand();
        }

        // Save changes to the database
        \XLite\Core\Database::getEM()->flush();
    }
}
