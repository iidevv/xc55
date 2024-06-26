<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Model\IndexingEventTriggers;

use QSL\CloudSearch\Core\IndexingEvent\IndexingEventCore;
use QSL\CloudSearch\Core\IndexingEvent\IndexingEventTriggerInterface;
use XLite\Model\AttributeOption;
use XC\MultiVendor\Model\Vendor;
use XC\ProductTags\Model\Tag;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Translation extends \XLite\Model\Base\Translation implements IndexingEventTriggerInterface
{
    public function getCloudSearchEntityType()
    {
        $owner = $this->getOwner();

        return ($owner instanceof Category)
            ? self::INDEXING_EVENT_CATEGORY_ENTITY
            : self::INDEXING_EVENT_PRODUCT_ENTITY;
    }

    public function getCloudSearchEntityIds()
    {
        $owner = $this->getOwner();

        if ($owner instanceof AttributeOption) {
            if (!$owner->getAttribute() || !$owner->getAttribute()->getProduct()) {
                return IndexingEventCore::findProductIdsByAttributeOption($owner);
            } else {
                return [$owner->getAttribute()->getProduct()->getProductId()];
            }

        } else if ($owner instanceof Attribute) {
            if (!$owner->getProduct()) {
                return IndexingEventCore::findProductIdsByAttribute($owner);
            } else {
                return [$owner->getProduct()->getProductId()];
            }
        } else if ($owner instanceof Product) {
            return [$owner->getProductId()];

        } else if ($owner instanceof Category) {
            return [$owner->getCategoryId()];

        } else if ($owner instanceof AAttributeValue) {
            return [$owner->getProduct()->getProductId()];

        } else if ($owner instanceof Tag) {
            return IndexingEventCore::findProductIdsByTag($owner);

        } else if ($owner instanceof Vendor) {
            return IndexingEventCore::findProductIdsByVendor($owner->getProfile());
        }

        return null;
    }

    public function getCloudSearchEventAction()
    {
        return self::INDEXING_EVENT_UPDATED_ACTION;
    }
}
