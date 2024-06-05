<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use QSL\CloudSearch\Core\IndexingEvent\IndexingEventListener;
use QSL\CloudSearch\Main;
use XCart\Extender\Mapping\Extender;

/**
 * Database
 *
 * @Extender\Mixin
 */
abstract class DatabaseDecorator extends \XLite\Core\Database
{
    /**
     * Start Doctrine entity manager
     *
     * @return void
     */
    public function startEntityManager()
    {
        parent::startEntityManager();

        if (!defined('LC_CACHE_BUILDING') && Main::isRealtimeIndexingEnabled()) {
            static::getEM()->getEventManager()->addEventSubscriber(new IndexingEventListener());
        }
    }
}
