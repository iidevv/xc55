<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Admin;

/**
 * Controller for the Recovery Statistics page.
 */
class CartRecoveryStats extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions.
     *
     * @return bool
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
        return static::t('Abandoned cart statistics');
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return 'QSL\AbandonedCartReminder\View\ItemsList\Table\RecoveredOrder';
    }
}
