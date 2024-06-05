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
use QSL\Returns\Core\Mail\OrderReturnCreatedVendor;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\Returns", "XC\MultiVendor"})
 */
abstract class MailerWithMultiVendor extends \XLite\Core\Mailer
{
    // {{{ Admin & Vendor emails

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

        if ($order->isParent()) {
            foreach ($order->getChildren() as $child) {
                static::getBus()->dispatch(new SendMail(OrderReturnCreatedVendor::class, [$child]));
            }
        } else {
            static::getBus()->dispatch(new SendMail(OrderReturnCreatedVendor::class, [$order]));
        }
    }

    // }}}
}
