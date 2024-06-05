<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCreateOrder\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    public static $useOrderProfileForMembership = false;

    public function getMembership()
    {
        if (static::$useOrderProfileForMembership
            && Request::getInstance()->order_id
        ) {

            $order = Database::getRepo('\XLite\Model\Order')->find(Request::getInstance()->order_id);

            if ($order && $order->getProfile()) {

                static::$useOrderProfileForMembership = false;
                $membership = $order->getProfile()->getMembership();
                static::$useOrderProfileForMembership = true;

                return $membership;
            }

        }

        return parent::getMembership();
    }
}