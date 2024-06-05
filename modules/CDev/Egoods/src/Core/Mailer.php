<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XLite\Model\Order;
use CDev\Egoods\Core\Mail\EgoodsLinkCustomer;

/**
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    /**
     * @param Order $order Order model
     */
    public static function sendEgoodsLinks(Order $order)
    {
        static::sendEgoodsLinksCustomer($order);
    }

    /**
     * @param Order $order Order model
     */
    public static function sendEgoodsLinksCustomer(Order $order)
    {
        static::getBus()->dispatch(new SendMail(EgoodsLinkCustomer::class, [$order]));
    }
}
