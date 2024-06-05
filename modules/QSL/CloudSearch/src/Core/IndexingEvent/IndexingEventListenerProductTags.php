<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core\IndexingEvent;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"XC\ProductTags"})
 */
class IndexingEventListenerProductTags extends IndexingEventListener
{
    protected function hasChanges($instance, PreUpdateEventArgs $eventArgs)
    {
        if ($instance instanceof Product
            && (count($instance->getTags()->getDeleteDiff())
                || count($instance->getTags()->getInsertDiff()))
        ) {
            return true;
        }

        return parent::hasChanges($instance, $eventArgs);
    }
}
