<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Customer;

use XPay\XPaymentsCloud\Main as XPaymentsHelper;
use XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XPaymentsCloud\Response;

/**
 * Subscriptions list controller
 */
class XpaymentsSubscriptions extends \XLite\Controller\Customer\ACustomer
{
    const XPAYMENTS_SUBSCRIPTIONS_TITLE = 'X-Payments subscriptions';

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->widget_title ?: static::t('My account');
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
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Define current location for breadcrumbs
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::XPAYMENTS_SUBSCRIPTIONS_TITLE;
    }

    /**
     * Changes card used for subscription
     *
     * @return void
     * @throws \XPaymentsCloud\ApiException
     */
    protected function doActionChangeCard()
    {
        $profile = $this->getProfile();

        $cardId = \XLite\Core\Request::getInstance()->card_id;
        $subscriptionId = \XLite\Core\Request::getInstance()->subscription_id;
        if ($subscriptionId) {
            $subscription = \XLite\Core\Database::getRepo('XPay\XPaymentsCloud\Model\Subscription\Subscription')
                ->find($subscriptionId);
        }

        if (
            $profile
            && \XLite\Core\Auth::getInstance()->isLogged()
            && $subscription
        ) {
            $updateParams = [
                'cardId' => $cardId,
            ];

            $api = XPaymentsHelper::getClient();
            $response = $api->doUpdateSubscription($subscription->getXpaymentsSubscriptionPublicId(), $updateParams);
            if ($response->getSubscription()) {
                $subscription->setCardId($cardId);
                \XLite\Core\Database::getEM()->flush();
            }
        }

        $this->setReturnURL($this->buildURL('xpayments_subscriptions'));
        $this->doRedirect();

    }


    /**
     * Stop/restart subscription actions common method
     *
     * @param string $status
     *
     * @return void
     * @throws \Exception
     */
    protected function changeSubscriptionStatus(string $status)
    {
        $subscriptionId = \XLite\Core\Request::getInstance()->subscription_id;
        if ($subscriptionId) {
            $subscription = \XLite\Core\Database::getRepo('XPay\XPaymentsCloud\Model\Subscription\Subscription')
                ->find($subscriptionId);
        }

        if (
            Subscription::STATUS_STOPPED == $status && $subscription->isActive()
            || Subscription::STATUS_RESTARTED == $status && $subscription->isRestartable()
        ) {
            $updateParams = [
                'status' => $status,
            ];

            $api = XPaymentsHelper::getClient();
            $response = $api->doUpdateSubscription($subscription->getXpaymentsSubscriptionPublicId(), $updateParams);
            if ($response->getSubscription()) {
                $subscription->setStatus($status);
                \XLite\Core\Database::getEM()->flush();
            }
        }

        $this->setReturnURL($this->buildURL('xpayments_subscriptions'));
        $this->doRedirect();
    }

    /**
     * Stops active subscription
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionStopSubscription()
    {
        $this->changeSubscriptionStatus(Subscription::STATUS_STOPPED);
    }

    /**
     * Restarts active subscription
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionRestartSubscription()
    {
        $this->changeSubscriptionStatus(Subscription::STATUS_RESTARTED);
    }

}
