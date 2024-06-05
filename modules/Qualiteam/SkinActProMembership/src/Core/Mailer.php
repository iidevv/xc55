<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Core;

use Qualiteam\SkinActProMembership\Core\Mail\BuyProMembership;
use Qualiteam\SkinActProMembership\Core\Mail\ProMembershipExpirationReminder;
use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;

/**
 * Decorated Mailer class.
 * @Extender\Mixin
 */
class Mailer extends \XLite\Core\Mailer
{
    public static function sendNotificationBuyProMembership(\XLite\Model\Profile $profile, $productId)
    {
        static::getBus()->dispatch(new SendMail(BuyProMembership::class, [$profile, $productId]));
    }

    public static function sendNotificationProMembershipExpirationReminder(\XLite\Model\OrderItem $item, $daysNum, $expDate = '')
    {
        static::getBus()->dispatch(new SendMail(ProMembershipExpirationReminder::class, [$item, $daysNum, $expDate]));
    }
}