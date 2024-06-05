<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\ItemsList\Model;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\Subscription as SubscriptionRepo;
use XLite\Core\CommonCell;
use XLite\Core\Request;

/**
 * Subscriptions items list
 */
class OrderSubscription extends Subscription
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $list = parent::defineColumns();
        $list['id'][static::COLUMN_TEMPLATE]
            = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/order/cell.id.twig';

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
        // magic (see \XLite\Controller\Admin\Order)
        $result->{SubscriptionRepo::SEARCH_ORDER}
            = $this->getOrder();
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
        $this->commonParams['order_number'] = Request::getInstance()->order_number;

        return $this->commonParams;
    }
}
