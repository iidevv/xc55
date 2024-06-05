<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View;

use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription as Subscription;
use XLite\Model\WidgetParam\TypeObject;
use XLite\View\AView;

/**
 * Account pin codes page order block
 *
 *
 */
class CustomerSubscription extends AView
{
    /**
     * Widget parameter names
     */
    const PARAM_SUBSCRIPTION = 'subscription';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/style.css';
        $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/script.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/subscription.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_SUBSCRIPTION => new TypeObject(
                'Subscription',
                null,
                false,
                '\\Qualiteam\\SkinActXPaymentsSubscriptions\\Model\\Subscription'
            ),
        ];
    }

    /**
     * Get subscription status class
     *
     * @param Subscription $subscription Subscription
     *
     * @return string
     */
    protected function getStatusClass($subscription)
    {
        return 'status-' . $subscription->getStatus();
    }

    /**
     * getStatusName
     *
     * @param Subscription $subscription Subscription
     *
     * @return string
     */
    protected function getStatusName($subscription)
    {
        $statuses = [
            ASubscriptionPlan::STATUS_NOT_STARTED => static::t('Not started'),
            ASubscriptionPlan::STATUS_RESTARTED   => static::t('Restarted'),
            ASubscriptionPlan::STATUS_ACTIVE      => static::t('Active'),
            ASubscriptionPlan::STATUS_STOPPED     => static::t('Stopped'),
            ASubscriptionPlan::STATUS_FAILED      => static::t('Failed'),
            ASubscriptionPlan::STATUS_FINISHED    => static::t('Finished'),
        ];

        return $statuses[$subscription->getStatus()];
    }

    /**
     * isLastPaymentFailed
     *
     * @param Subscription $subscription Subscription
     *
     * @return boolean
     */
    protected function isLastPaymentFailed($subscription)
    {
        return ASubscriptionPlan::STATUS_FAILED !== $subscription->getStatus()
            && $subscription->getRealDate() > $subscription->getPlannedDate();
    }

    /**
     * isLastPaymentExpired
     *
     * @param Subscription $subscription Subscription
     *
     * @return boolean
     */
    protected function isLastPaymentExpired($subscription)
    {
        return $subscription->getRealDate() < Converter::now();
    }

    /**
     * isNextDateVisible
     *
     * @param Subscription $subscription Subscription
     *
     * @return boolean
     */
    protected function isNextDateVisible($subscription)
    {
        return ASubscriptionPlan::STATUS_NOT_STARTED !== $subscription->getStatus()
            && ASubscriptionPlan::STATUS_FINISHED !== $subscription->getStatus()
            && ASubscriptionPlan::STATUS_STOPPED !== $subscription->getStatus()
            && ASubscriptionPlan::STATUS_FAILED !== $subscription->getStatus();
    }

    /**
     * Define line class as list of names
     *
     * @param Subscription $subscription Subscription
     *
     * @return array
     */
    protected function getLineClass($subscription)
    {
        $class = '';

        if ($this->isLastPaymentFailed($subscription)) {
            $class = 'last-payment-failed';
        }

        if ($this->isLastPaymentExpired($subscription)) {
            $class = 'last-payment-expired';
        }

        return $class;
    }
}
