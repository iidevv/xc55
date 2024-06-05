<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\ItemsList\Model\Customer;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\Subscription as SubscriptionRepo;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\View\ItemsList\AItemsList;

/**
 * Account pin codes based on orders
 *
 */
class Subscription extends AItemsList
{
    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/list.js';

        return $list;
    }

    /**
     * Define widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {   
        return 'modules/Qualiteam/SkinActXPaymentsSubscriptions';
    }

    /**
     * Define page body templates directory
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'subscription';
    }

    /**
     * @return string
     */
    protected function getListHead()
    {
        return $this->getItemsCount()
            ? static::t('X subscriptions', ['count' => $this->getItemsCount()])
            : static::t('No subscriptions');
    }

    /**
     * @return boolean
     */
    protected function isHeadVisible()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return \Qualiteam\SkinActXPaymentsSubscriptions\View\Pager\Customer\Subscription::class;
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->hasResults();
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return string
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Return products list
     *
     * @param CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(CommonCell $cnd, $countOnly = false)
    {
        $cnd->{SubscriptionRepo::SEARCH_PROFILE} = Auth::getInstance()->getProfile();
        $cnd->{SubscriptionRepo::SEARCH_ORDER_BY} = ['s.id', 'DESC'];

        return Database::getRepo(\Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription::class)
            ->search($cnd, $countOnly);
    }
}
