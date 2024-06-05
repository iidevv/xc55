<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Layout manager
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    /**
     * @return array
     */
    protected function getSidebarFirstHiddenTargets()
    {
        return array_merge(
            parent::getSidebarFirstHiddenTargets(),
            [
                'wishlist',
            ]
        );
    }

    /**
     * @return array
     */
    protected function getSidebarSecondHiddenTargets()
    {
        return array_merge(
            parent::getSidebarSecondHiddenTargets(),
            [
                'wishlist',
            ]
        );
    }
}
