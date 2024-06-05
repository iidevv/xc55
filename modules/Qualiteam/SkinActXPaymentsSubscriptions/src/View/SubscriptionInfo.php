<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription as Subscription;

/**
 * Subscription info view
 *
 */
class SubscriptionInfo extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription_info/body.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription_info/style.css';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getSubscription();
    }

    /**
     * Return subscription entity
     *
     * @return Subscription
     */
    protected function getSubscription()
    {
        $subscriptionId = isset(\XLite\Core\Request::getInstance()->subscription_id)
            ? \XLite\Core\Request::getInstance()->subscription_id
            : 0;

        return \XLite\Core\Database::getRepo(Subscription::class)
            ->find($subscriptionId);
    }

    /**
     * Get formatted date string
     *
     * @param integer $time Time
     *
     * @return string
     */
    protected function getTime($time)
    {
        return \XLite\Core\Converter::getInstance()->formatDate(intval($time));
    }

    /**
     * Get formatted subscription status
     *
     * @return string
     */
    protected function getFormattedStatus()
    {
        $statuses = [
            ASubscriptionPlan::STATUS_NOT_STARTED => static::t('Not started'),
            ASubscriptionPlan::STATUS_RESTARTED   => static::t('Restarted'),
            ASubscriptionPlan::STATUS_ACTIVE      => static::t('Active'),
            ASubscriptionPlan::STATUS_STOPPED     => static::t('Stopped'),
            ASubscriptionPlan::STATUS_FAILED      => static::t('Failed'),
            ASubscriptionPlan::STATUS_FINISHED    => static::t('Finished'),
        ];

        return $statuses[$this->getSubscription()->getStatus()];
    }

    /**
     * Details array for widget
     *
     * @return array
     */
    protected function getDetails()
    {
        $details = array_filter([
            'Subscription status' => $this->getFormattedStatus(),
            'Product name' => $this->getSubscription()->getProductName(),
            'Start date' => $this->getTime($this->getSubscription()->getStartDate()),
            'Next payment date' =>
                $this->getSubscription()->getStatus() === ASubscriptionPlan::STATUS_FINISHED
                    ? false
                    : $this->getTime($this->getSubscription()->getPlannedDate()),
            'Successful payments' => $this->getSubscription()->getSuccessTries(),
            'Calculate shipping' => ($this->getSubscription()->getCalculateShipping())
                ? 'Yes'
                : 'No',
        ]);

        if ($this->getSubscription()->getFailedTries()) {
            $details['Failed attempts for last payment'] = $this->getSubscription()->getFailedTries();
        }

        if ($this->getSubscription()->getPeriods()) {
            $details['Number of remaining payments'] = $this->getSubscription()->getRemainingPayments();
        }

        return $details;
    }
}
