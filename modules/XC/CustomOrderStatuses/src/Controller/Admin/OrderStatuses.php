<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\Controller\Admin;

class OrderStatuses extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Settings');
    }

    /**
     * Get page type
     *
     * @return string
     */
    public function getPageType()
    {
        return in_array($this->page, ['payment', 'shipping']) ? $this->page : null;
    }

    /**
     * @return string
     */
    public function getItemsListClass()
    {
        return 'XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\\' . ucfirst($this->getPageType());
    }
}
