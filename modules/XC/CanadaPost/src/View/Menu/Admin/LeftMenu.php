<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\Menu\Admin;

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
        if (!isset($this->relatedTargets['shipping_methods'])) {
            $this->relatedTargets['shipping_methods'] = [];
        }

        $this->relatedTargets['shipping_methods'][] = 'capost';

        parent::__construct($params);
    }
}
