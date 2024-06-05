<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use XCart\Event\Service\FixturesPostLoadEvent;

final class FixturesLoadedListener
{
    public function handlePostLoad(FixturesPostLoadEvent $event): void
    {
        \XLite\Core\Database::getRepo('XLite\Model\Category')->correctCategoriesStructure();
    }
}
