<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View;

use XCart\Extender\Mapping\Extender;

/**
 * Controller main widget
 * @Extender\Mixin
 */
class Controller extends \XLite\View\Controller
{
    /**
     * Check - first sidebar is visible or not (in customer interface)
     *
     * @return boolean
     */
    public static function isSidebarSecondVisible()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        $visible = in_array(\XLite\Core\Request::getInstance()->target, ['loyalty_program_details'])
            && (!$profile || !$profile->isLoyaltyProgramEnabled());

        return $visible || parent::isSidebarSecondVisible();
    }

    /**
     * Check - first sidebar is visible or not (in customer interface)
     *
     * @return boolean
     */
    protected static function isCustomerSidebarFirstVisible()
    {
        return parent::isCustomerSidebarFirstVisible()
            && !in_array(\XLite\Core\Request::getInstance()->target, ['loyalty_program_details']);
    }
}
