<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Module\XC\ThemeTweaker\Core\Notifications\Data;

use XCart\Extender\Mapping\Extender;

/**
 * Order
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class Order extends \XC\ThemeTweaker\Core\Notifications\Data\Order
{
    protected function getTemplateDirectories()
    {
        return array_merge(parent::getTemplateDirectories(), [
            'modules/QSL/CustomerSatisfaction/customer_notification',
            'modules/QSL/CustomerSatisfaction/feedback',
        ]);
    }
}
