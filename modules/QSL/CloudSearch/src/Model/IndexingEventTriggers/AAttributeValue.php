<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Model\IndexingEventTriggers;

use QSL\CloudSearch\Core\IndexingEvent\IndexingEventCore;
use QSL\CloudSearch\Core\IndexingEvent\IndexingEventTriggerInterface;
use XCart\Extender\Mapping\Extender;

/**
 * Abstract attribute value
 *
 * @Extender\Mixin
 */
abstract class AAttributeValue extends \XLite\Model\AttributeValue\AAttributeValue  implements IndexingEventTriggerInterface
{
    public function getCloudSearchEntityType()
    {
        return self::INDEXING_EVENT_PRODUCT_ENTITY;
    }

    public function getCloudSearchEntityIds()
    {
        if (!$this->getProduct()) {
            return $this->getAttribute() ? IndexingEventCore::findProductIdsByAttribute($this->getAttribute()) : [];
        } else {
            return [$this->getProduct()->getProductId()];
        }
    }

    public function getCloudSearchEventAction()
    {
        return self::INDEXING_EVENT_UPDATED_ACTION;
    }
}
