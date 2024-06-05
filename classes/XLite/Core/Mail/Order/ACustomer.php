<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Order;

use XLite\Core\Mailer;
use XLite\Model\Order;

abstract class ACustomer extends \XLite\Core\Mail\Order\AOrder
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public function __construct(Order $order)
    {
        parent::__construct($order);
        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->setReplyTo(Mailer::getOrdersDepartmentMails());
        $this->setTo(['email' => $order->getProfile()->getEmail(), 'name' => $order->getProfile()->getName(false)]);
        $this->tryToSetLanguageCode($order->getProfile()->getLanguage());
    }
}
