<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Controller\Customer;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use XLite\Controller\Customer\ACustomer;
use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 * Subscriptions list controller
 */
class XPaymentsSubscription extends ACustomer
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return Request::getInstance()->widget_title ?: static::t('My account');
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return $this->checkAccess();
    }

    /**
     * Define current location for breadcrumbs
     *
     * @return string
     */
    protected function getLocation()
    {
        return 'Subscriptions';
    }

    /**
     * Get current logged user profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }

    /**
     * Changes card used for subscription
     *
     * @return void
     */
    protected function doActionChangeCard()
    {
        $profile = $this->getProfile();

        $cardId = Request::getInstance()->card_id;
        $subscriptionId = Request::getInstance()->subscription_id;

        if (
            $profile
            && $profile->isCardIdValid($cardId)
            && Auth::getInstance()->isLogged()
        ) {
            $card = Database::getRepo(XpcTransactionData::class)
                ->find($cardId);
            
            $subscription = Database::getRepo(Subscription::class)
                ->find($subscriptionId);

            if ($subscription) {
                $subscription->setXpcData($card);
                Database::getEM()->flush();
            }
        }

        $this->setReturnURL($this->buildURL('x_payments_subscription'));
        $this->doRedirect();
    }


    /**
     * Stop/restart subscription actions common method
     *
     * @param string $status
     *
     * @return void
     */
    protected function changeSubscriptionStatus($status)
    {
        $subscription = Database::getRepo(Subscription::class)
            ->find(Request::getInstance()->subscription_id);

        if (
            ($status == ASubscriptionPlan::STATUS_STOPPED && $subscription->isActive())
            ||
            ($status == ASubscriptionPlan::STATUS_RESTARTED && $subscription->isRestartable())
        ) {
            $subscription->setStatus($status);
            Database::getEM()->flush();
        }

        $this->setReturnURL($this->buildURL('x_payments_subscription'));
        $this->doRedirect();
    }

    /**
     * Stops active subscription
     *
     * @return void
     */
    protected function doActionStopSubscription()
    {
        $this->changeSubscriptionStatus(ASubscriptionPlan::STATUS_STOPPED);
    }

    /**
     * Restarts active subscription
     *
     * @return void
     */
    protected function doActionRestartSubscription()
    {
        $this->changeSubscriptionStatus(ASubscriptionPlan::STATUS_RESTARTED);
    }
}
