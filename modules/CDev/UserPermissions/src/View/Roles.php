<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\UserPermissions\View;

class Roles extends \XLite\View\Dialog
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['roles']);
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/UserPermissions/roles';
    }
}
