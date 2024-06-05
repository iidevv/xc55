<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Layout manager
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    /**
     * Define the pages where first sidebar will be hidden.
     *
     * @return array
     */
    protected function getSidebarFirstHiddenTargets()
    {
        $list = parent::getSidebarFirstHiddenTargets();
        $list[] = 'main';

        return $list;
    }


}
