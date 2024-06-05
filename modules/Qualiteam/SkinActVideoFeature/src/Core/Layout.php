<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    protected function getSidebarFirstHiddenTargets()
    {
        return array_merge(
            parent::getSidebarFirstHiddenTargets(),
            [
                'educational_videos',
            ]
        );
    }

    protected function getSidebarSecondHiddenTargets()
    {
        return array_merge(
            parent::getSidebarSecondHiddenTargets(),
            [
                'educational_videos',
            ]
        );
    }
}