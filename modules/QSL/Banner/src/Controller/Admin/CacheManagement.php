<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Controller\Admin;

/**
 * Class to make doActionRebuildViewLists public
 */
class CacheManagement extends \XLite\Controller\Admin\CacheManagement
{
    public function rebuildViewLists()
    {
        return parent::doActionRebuildViewLists();
    }
}
