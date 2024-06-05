<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Model\IndexingEventTriggers\Image\Category;

use QSL\CloudSearch\Core\IndexingEvent\IndexingEventTriggerInterface;
use XCart\Extender\Mapping\Extender;


/**
 * Product image
 *
 * @Extender\Mixin
 */
class Image extends \XLite\Model\Image\Category\Image  implements IndexingEventTriggerInterface
{
    public function getCloudSearchEntityType()
    {
        return self::INDEXING_EVENT_CATEGORY_ENTITY;
    }

    public function getCloudSearchEntityIds()
    {
        return [$this->getCategory()->getCategoryId()];
    }

    public function getCloudSearchEventAction()
    {
        return self::INDEXING_EVENT_UPDATED_ACTION;
    }
}