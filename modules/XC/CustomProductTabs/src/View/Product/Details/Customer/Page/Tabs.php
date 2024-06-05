<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * Product tabs
 * @Extender\Mixin
 */
class Tabs extends \XLite\View\Product\Details\Customer\Page\Tabs
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();
        $params[] = \XLite\Core\Database::getRepo('XC\CustomProductTabs\Model\Product\Tab')->getVersion();
        $params[] = \XLite\Core\Database::getRepo('XC\CustomProductTabs\Model\Product\CustomGlobalTab')->getVersion();

        return $params;
    }
}
