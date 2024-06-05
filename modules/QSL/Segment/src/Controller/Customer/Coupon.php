<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Coupon
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class Coupon extends \CDev\Coupons\Controller\Customer\Coupon
{
    /**
     * @inheritdoc
     */
    protected function doActionAdd()
    {
        $old = $this->getCart()->getUsedCoupons()->toArray();

        parent::doActionAdd();

        \QSL\Segment\Core\Mediator::getInstance()
            ->processCouponsDifference($old, $this->getCart()->getUsedCoupons()->toArray());
    }

    /**
     * @inheritdoc
     */
    protected function doActionRemove()
    {
        $old = $this->getCart()->getUsedCoupons()->toArray();

        parent::doActionRemove();

        \QSL\Segment\Core\Mediator::getInstance()
            ->processCouponsDifference($old, $this->getCart()->getUsedCoupons()->toArray());
    }
}
