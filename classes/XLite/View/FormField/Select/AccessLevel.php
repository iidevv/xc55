<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

use XLite\Core\Auth;

/**
 * \XLite\View\FormField\Select\AccessLevel
 */
class AccessLevel extends \XLite\View\FormField\Select\Regular
{
    /**
     * Determines if this field is visible for customers or not
     *
     * @var boolean
     */
    protected $isAllowedForCustomer = false;


    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = Auth::getInstance()->getUserTypesRaw();
        $isAllowedToManageAdmins = Auth::getInstance()->isPermissionAllowed('manage admins');
        $isAllowedToManageCustomers = Auth::getInstance()->isPermissionAllowed('manage users');
        $adminAccessLevel = Auth::getInstance()->getAdminAccessLevel();
        $customerAccessLevel = Auth::getInstance()->getCustomerAccessLevel();

        foreach ($list as $k => $v) {
            if (
                (!$isAllowedToManageAdmins && $k === $adminAccessLevel)
                || (!$isAllowedToManageCustomers && $k === $customerAccessLevel)
            ) {
                unset($list[$k]);
            } else {
                $list[$k] = static::t($v);
            }
        }

        return $list;
    }

    /**
     * Check field value validity
     *
     * @return boolean
     */
    protected function checkFieldValue()
    {
        $isAllowedForCurrentUser = true;
        if (
            (
                !Auth::getInstance()->isPermissionAllowed('manage admins')
                && $this->getValue() == Auth::getInstance()->getAdminAccessLevel()
            )
            || (
                !Auth::getInstance()->isPermissionAllowed('manage users')
                && $this->getValue() == Auth::getInstance()->getCustomerAccessLevel()
            )
        ) {
            $isAllowedForCurrentUser = false;
        }
        return $isAllowedForCurrentUser && in_array($this->getValue(), Auth::getInstance()->getAccessLevelsList());
    }
}
