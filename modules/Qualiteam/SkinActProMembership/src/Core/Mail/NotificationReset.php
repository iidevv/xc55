<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActProMembership\Core\Mail;

use XCart\Extender\Mapping\Extender;
use XLite\Model\OrderItem;

/**
 * @Extender\Mixin
 */
class NotificationReset extends \QSL\MembershipProducts\Core\Mail\NotificationReset
{

    public function __construct(OrderItem $item = null)
    {
        parent::__construct($item);

        if ($item && $item->getOrder()->getProfile()) {
            $profile = $item->getOrder()->getProfile();
            $this->setTo($profile->getLogin());
        }
    }
}