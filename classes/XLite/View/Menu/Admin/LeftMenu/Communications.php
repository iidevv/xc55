<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

use XLite\Core\View\DynamicWidgetInterface;

/**
 * Sales
 */
class Communications extends \XLite\View\Menu\Admin\LeftMenu\ANode implements DynamicWidgetInterface
{
    /**
     * @return bool
     */
    protected function showLabel()
    {
        return false;
    }
}
