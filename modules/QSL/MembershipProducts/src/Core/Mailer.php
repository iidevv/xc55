<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;

/**
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    /**
     * Send assigned membership message
     *
     * @param \XLite\Model\OrderItem $item Order item
     */
    public function sendAssignedMembershipProductNotification(\XLite\Model\OrderItem $item)
    {
        static::getBus()->dispatch(new SendMail(Mail\NotificationAssigned::class, [$item]));
    }

    /**
     * Send reset membership message
     *
     * @param \XLite\Model\OrderItem $item Order item
     */
    public function sendResetMembershipProductNotification(\XLite\Model\OrderItem $item)
    {
        static::getBus()->dispatch(new SendMail(Mail\NotificationReset::class, [$item]));
    }
}
