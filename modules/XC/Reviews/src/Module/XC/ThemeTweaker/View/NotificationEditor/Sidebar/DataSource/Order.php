<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource;

use XCart\Extender\Mapping\Extender;

/**
 * Order
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class Order extends \XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource\Order
{
    protected static function getTemplateDirectories()
    {
        return array_merge(parent::getTemplateDirectories(), [
            'modules/XC/Reviews/review_key',
        ]);
    }
}
