<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Layout\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class SidebarFirst extends \XLite\View\Layout\Customer\SidebarFirst
{
    /**
     * @param $content
     *
     * @return bool
     */
    protected function isEmptyContent($content)
    {
        return $this->isInLayoutMode()
            ? ($content === "<div class='list-items-group' data-list='{$this->getDefaultInnerList()}'></div>")
            : parent::isEmptyContent($content);
    }
}
