<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View\Menu\Admin;

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
        if (!isset($this->relatedTargets['global_attributes'])) {
            $this->relatedTargets['global_attributes'] = [];
        }

        $this->relatedTargets['global_attributes'][] = 'tags';

        parent::__construct($params);
    }
}
