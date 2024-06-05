<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\Core;

use Qualiteam\SkinActCustomerReviews\Core\Mail\ReportAbuse;
use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;

/**
 * Decorated Mailer class.
 * @Extender\Mixin
 */
class Mailer extends \XLite\Core\Mailer
{
    public static function sendNotificationAbuseReport($review)
    {
        static::getBus()->dispatch(new SendMail(ReportAbuse::class, [$review]));
    }
}