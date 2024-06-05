<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Module\CDev\Coupons\View\ItemsList;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Coupons extends \CDev\Coupons\View\ItemsList\Coupons
{
    public function getCSSFiles()
    {
        $res = parent::getCSSFiles();
        $res[] = 'modules/QSL/AbandonedCartReminder/coupon/style.css';

        return $res;
    }
}
