<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Module\CDev\SimpleCMS\View\ItemsList\Model\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SimpleCMS")
 */
class Tab extends \XC\CustomProductTabs\View\ItemsList\Model\Product\Tab
{
    /**
     * Returns global tabs link
     *
     * @return string
     */
    public function getGlobalTabsLink()
    {
        return $this->buildURL('pages', '', ['page' => 'global_tabs']);
    }
}
