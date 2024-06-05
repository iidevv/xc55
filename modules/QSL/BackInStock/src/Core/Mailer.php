<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use QSL\BackInStock\Core\Mail\NotificationPrice;
use QSL\BackInStock\Core\Mail\NotificationStock;
use QSL\BackInStock\Model\Record;
use QSL\BackInStock\Model\RecordPrice;

/**
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    /**
     * Send back in stock message
     *
     * @param Record $record Record
     *
     * @return void
     */
    public static function sendBackInStockNotification(Record $record)
    {
        static::getBus()->dispatch(new SendMail(NotificationStock::class, [$record]));
    }

    /**
     * Send low price message
     *
     * @param RecordPrice $record Record
     *
     * @return void
     */
    public static function sendLowPriceNotification(RecordPrice $record)
    {
        static::getBus()->dispatch(new SendMail(NotificationPrice::class, [$record]));
    }
}
