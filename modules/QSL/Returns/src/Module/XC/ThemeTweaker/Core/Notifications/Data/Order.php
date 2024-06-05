<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Module\XC\ThemeTweaker\Core\Notifications\Data;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class Order extends \XC\ThemeTweaker\Core\Notifications\Data\Order
{
    protected function getTemplateDirectories()
    {
        return array_merge(
            parent::getTemplateDirectories(),
            [
                'modules/QSL/Returns/return/completed',
                'modules/QSL/Returns/return/created',
                'modules/QSL/Returns/return/declined'
            ]
        );
    }
}
