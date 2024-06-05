<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Admin;

class LoyaltyProgram extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions.
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Reward points');
    }
}
