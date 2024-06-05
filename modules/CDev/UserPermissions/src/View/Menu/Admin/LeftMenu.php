<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\UserPermissions\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['profile_list'])) {
            $this->relatedTargets['profile_list'] = [];
        }

        $this->relatedTargets['profile_list'][] = 'roles';
        $this->relatedTargets['profile_list'][] = 'role';

        parent::__construct($params);
    }
}
