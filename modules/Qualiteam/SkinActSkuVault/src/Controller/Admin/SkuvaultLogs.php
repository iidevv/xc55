<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Controller\Admin;

use Qualiteam\SkinActSkuVault\View\ItemsList\Logs;
use XLite\Controller\Features\SearchByFilterTrait;
use XLite\Core\Request;

class SkuvaultLogs extends SkuVault
{
    use SearchByFilterTrait;

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return Request::getInstance()->itemsList ?: Logs::class;
    }
}
