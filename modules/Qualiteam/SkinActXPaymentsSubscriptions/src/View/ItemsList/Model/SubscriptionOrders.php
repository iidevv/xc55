<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\ItemsList\Model;

use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Repo\Order;
use XLite\View\ItemsList\Model\Order\Admin\Search;

/**
 * Search order
 */
class SubscriptionOrders extends Search
{
    /**
     * getSubscriptionId
     *
     * @return integer
     */
    protected function getSubscriptionId()
    {
        return Request::getInstance()->subscription_id;
    }

    /**
     * getSubscription
     *
     * @return \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription
     */
    protected function getSubscription()
    {
        $subscriptionId = $this->getSubscriptionId();

        return Database::getRepo(\Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription::class)
            ->find($subscriptionId);
    }

    /**
     * Return params list to use for search
     *
     * @return CommonCell
     */
    protected function getSearchCondition()
    {
        $result = new CommonCell();
        // SEARCH_SUBSCRIPTION defined in \Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\Order
        $result->{Order::SEARCH_SUBSCRIPTION} = $this->getSubscription();

        return $result;
    }
}
