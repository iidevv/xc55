<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\TopMessage;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
abstract class Promotions extends \XLite\Controller\Admin\Promotions
{
    /**
     * Update list
     *
     * @return void
     */
    protected function doActionDeleteExpiredCoupons()
    {
        $deleted = Database::getRepo('CDev\Coupons\Model\Coupon')
            ->deleteExpiredCoupons(Converter::time());

        TopMessage::addInfo(static::t(
            'Number of expired coupons that have been deleted: X',
            ['count' => $deleted]
        ));
    }
}
