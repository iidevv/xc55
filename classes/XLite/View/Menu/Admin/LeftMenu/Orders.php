<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

/**
 * Sales
 */
class Orders extends \XLite\View\Menu\Admin\LeftMenu\Sales implements \XLite\Base\IDecorator
{
    /**
     * @return bool
     */
    protected function showLabel()
    {
        return true;
    }
}
