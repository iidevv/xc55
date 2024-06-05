<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Logic;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class MembershipProducts extends \QSL\MembershipProducts\Logic\MembershipProducts
{

    public function applyMembershipProduct(\XLite\Model\Order $order)
    {
        if (in_array($order->getPaymentStatusCode(), \XLite\Model\Order\Status\Payment::getPaidStatuses(), true)) {
            return parent::applyMembershipProduct($order);
        }

        return false;
    }
}