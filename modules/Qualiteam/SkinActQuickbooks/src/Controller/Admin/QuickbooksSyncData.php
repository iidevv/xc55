<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

use XLite\Controller\Admin\AAdmin;
use XLite\Core\Request;

class QuickbooksSyncData extends AAdmin
{
    /**
     * Get title
     * 
     * @return string
     */
    public function getTitle()
    {
        return static::t('QuickBooks Sync Data');
    }
    
    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }
    
    /**
     * doNoAction
     * 
     * @return void
     */
    public function doNoAction()
    {
        parent::doNoAction();
        
        $target = Request::getInstance()->target;
        
        if ('quickbooks_sync_data' == $target) {
            $this->setReturnURL($this->buildURL('quickbooks_sync_orders'));
        }
    }
}