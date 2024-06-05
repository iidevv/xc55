<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\SearchPanel\Order\Admin;

use XCart\Extender\Mapping\Extender;
use Qualiteam\SkinActQuickbooks\View\ItemsList\QuickbooksSyncOrders;
use Qualiteam\SkinActQuickbooks\View\ItemsList\QuickbooksSyncErrors;

/**
 * Main admin orders list search panel
 * 
 * @Extender\Mixin
 */
class Main extends \XLite\View\SearchPanel\Order\Admin\Main
{
    /**
     * Define search filters options
     * TODO: Review and correct before commit!
     *
     * @return array
     */
    protected function defineFilterOptions()
    {
        $result = parent::defineFilterOptions();

        $itemsList = $this->getItemsList();

        if (
            ($itemsList instanceof QuickbooksSyncOrders
            || $itemsList instanceOf QuickbooksSyncErrors)
            && isset($result['recent'])
        ) {
            unset($result['recent']);
        }

        return $result;
    }
}