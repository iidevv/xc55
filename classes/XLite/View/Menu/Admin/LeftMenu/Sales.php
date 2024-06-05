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
class Sales extends \XLite\View\Menu\Admin\LeftMenu\ANode implements DynamicWidgetInterface
{
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|int|string|null
     */
    protected function getLabel()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order')->searchRecentOrders(null, true)
            ?: null;
    }

    /**
     * @return bool
     */
    protected function showLabel()
    {
        return false;
    }

    /**
     * @return array|string[]
     */
    protected function getCacheParameters()
    {
        return array_merge(parent::getCacheParameters(), [
            \XLite\Core\Database::getRepo('XLite\Model\Order')->getVersion(),
        ]);
    }
}
