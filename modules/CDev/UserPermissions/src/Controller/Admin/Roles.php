<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\UserPermissions\Controller\Admin;

class Roles extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Users');
    }
}
