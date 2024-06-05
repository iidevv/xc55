<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XLite\Model\Order;
use QSL\Returns\Core\Mail\OrderReturnCreatedAdmin;
use QSL\Returns\Core\Mail\OrderReturnCompleted;
use QSL\Returns\Core\Mail\OrderReturnDeclined;
use QSL\Returns\Core\Mail\OrderReturnCreated;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    // {{{ Admin emails

    /**
     * Send 'Return created' mail
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendOrderReturnCreatedAdmin(Order $order)
    {
        static::getBus()->dispatch(new SendMail(OrderReturnCreatedAdmin::class, [$order]));
    }

    // }}}


    // {{{ Customer emails

    /**
     * Send 'Return created' mail
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendOrderReturnCreatedCustomer(Order $order)
    {
        static::getBus()->dispatch(new SendMail(OrderReturnCreated::class, [$order]));
    }

    /**
     * Send 'Return completed' mail
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendOrderReturnCompleted(Order $order)
    {
        static::getBus()->dispatch(new SendMail(OrderReturnCompleted::class, [$order]));
    }

    /**
     * Send 'Return declined' mail
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendOrderReturnDeclined(Order $order)
    {
        static::getBus()->dispatch(new SendMail(OrderReturnDeclined::class, [$order]));
    }

    // }}}
}
