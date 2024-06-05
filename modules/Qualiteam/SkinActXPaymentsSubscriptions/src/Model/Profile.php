<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Doctrine\Common\Collections\Collection;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\Subscription as SubscriptionRepo;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Database;

/**
 * The "profile" model class
 *
 * @Extender\Mixin
 */
abstract class Profile extends \XLite\Model\Profile
{
    /**
     * Has subscriptions
     *
     * @return boolean
     */
    public function hasSubscriptions()
    {
        $result = false;

        foreach ($this->getOrders() as $order) {
            if ($order->hasSubscriptions()) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get orders
     *
     * @return Collection
     */
    protected function getOrders()
    {
        $cnd = new CommonCell();
        $cnd->profile = $this;

        return Database::getRepo('XLite\Model\Order')->search($cnd);
    }

    public function hasOldXpaymentsSubscriptions()
    {
        $cnd = new CommonCell();
        $cnd->{SubscriptionRepo::SEARCH_PROFILE} = $this;

        return Database::getRepo(Subscription::class)
                ->search($cnd, true) > 0;
    }
}
