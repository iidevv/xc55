<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\ItemsList\Model;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\Subscription as SubscriptionRepo;
use XLite\Core\CommonCell;

/**
 * Subscriptions items list
 */
class ProfileSubscription extends Subscription
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $list = parent::defineColumns();
        $list['product'][static::COLUMN_MAIN] = true;
        unset($list['profile']);

        return $list;
    }

    /**
     * Get search condition
     *
     * @return CommonCell
     */
    protected function getSearchCondition()
    {
        $result = new CommonCell();
        // magic (see \Qualiteam\SkinActXPaymentsSubscriptions\Controller\Admin\XPaymentsUserSubscription)
        $result->{SubscriptionRepo::SEARCH_PROFILE}
            = $this->getProfile();
        $result->{SubscriptionRepo::SEARCH_ORDER_BY} = $this->getOrderBy();

        return $result;
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['profile_id'] = $this->getProfileId();

        return $this->commonParams;
    }
}
