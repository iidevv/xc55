<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CacheManagement extends \XLite\Controller\Admin\CacheManagement
{
    /**
     * Export action
     *
     * @return void
     */
    protected function doActionQuickData()
    {
        parent::doActionQuickData();

        \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu')->recalculateTreeStructure();
    }
}
