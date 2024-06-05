<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Controller\Admin;

/**
 * Global tabs controller
 */
class GlobalTabs extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Product page tabs');
    }

    /**
     * Process global tabs update
     */
    protected function doActionUpdateGlobalTabs()
    {
        $list = new \XC\CustomProductTabs\View\ItemsList\Model\GlobalTabs();
        $list->processQuick();

        if (\XLite\Core\Request::getInstance()->global_update) {
            \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->updateAliases();
        }
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog');
    }
}
