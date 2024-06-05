<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Widget cache manager serves as a factory of WidgetCache and also as an implementation of WidgetCacheRegistryInterface.
 *
 * TODO: current WidgetCacheRegistryInterface implementation is prone to race conditions, rewrite without separate registry file (for example, deleteAll can just remove cache entries having certain prefix).
 */
class WidgetCacheManager implements WidgetCacheRegistryInterface
{
    /**
     * Delete all
     *
     * @return boolean
     */
    public function deleteAll()
    {
        /** @var WidgetCache $widgetCache */
        $widgetCache = \XCart\Container::getContainer()->get(WidgetCache::class);
        $widgetCache->deleteAll();
    }

    /**
     * Invalidate widget cache based on entity types that were changed (inserted, updated or removed) during the current request.
     */
    public function invalidateBasedOnDatabaseChanges()
    {
        $notAffectingEntities = [
            'XLite\Model\TmpVar',
            'XLite\Model\Payment\Transaction',
            'XLite\Model\NotificationTranslation',
            'XLite\Model\Notification',
            'XLite\Model\ConfigTranslation',
        ];

        $entityTypes = \XLite\Core\Database::getRepo('XLite\Model\EntityTypeVersion')->getBumpedEntityTypes();

        if (array_diff($entityTypes, $notAffectingEntities)) {
            $this->deleteAll();
        }
    }
}
