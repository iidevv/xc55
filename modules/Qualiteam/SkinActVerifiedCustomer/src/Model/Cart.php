<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Cart
 * @Extender\Mixin
 */
abstract class Cart extends \XLite\Model\Cart
{
    /**
     * Mark cart as order
     *
     * @return void
     */
    public function markAsOrder()
    {
        parent::markAsOrder();

        if (\XLite\Core\Config::getInstance()->Qualiteam->SkinActVerifiedCustomer->change_fulfillment > 0) {

            $sid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActVerifiedCustomer->order_verified_status_id;

            $status = Database::getRepo('\XLite\Model\Order\Status\Shipping')->find($sid);

            $profile = $this->getOrigProfile();

            if ($status
                && $profile
                && $profile->isVerified()
            ) {
                $this->setShippingStatus($status);

                $history = \XLite\Core\OrderHistory::getInstance();

                $history->registerEvent(
                    $this->getOrderId(),
                    'CF',
                    'SkinActVerifiedCustomer change_fulfillment event'
                );
            }

        }
    }
}
