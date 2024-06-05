<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UpdateInventory\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['import'])) {
            $this->relatedTargets['import'] = [];
        }

        $this->relatedTargets['import'][] = \XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY;

        parent::__construct($params);
    }
}
