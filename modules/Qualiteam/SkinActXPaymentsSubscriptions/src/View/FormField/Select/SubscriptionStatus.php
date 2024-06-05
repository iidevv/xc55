<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Select;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\Subscription as SubscriptionRepo;
use XLite\Model\WidgetParam\TypeBool;
use XLite\View\FormField\Select\Regular;

/**
 * Subscription status selector
 */
class SubscriptionStatus extends Regular
{
    /**
     * Widget param names
     */
    const PARAM_DISPLAY_SEARCH_STATUSES = 'displaySearchStatuses';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DISPLAY_SEARCH_STATUSES => new TypeBool(
                'Display \search related statuses',
                false
            ),
        ];
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();

        if ($this->getParam(static::PARAM_DISPLAY_SEARCH_STATUSES)) {
            $list = [
                    SubscriptionRepo::STATUS_ANY => static::t('Any status'),
                ]
                + $list
                + [
                    SubscriptionRepo::STATUS_EXPIRED       => static::t('Expired'),
                    SubscriptionRepo::STATUS_ACTIVE_FAILED => static::t('Active, with failed transaction'),
                ];
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ASubscriptionPlan::STATUS_ACTIVE      => static::t('Active'),
            ASubscriptionPlan::STATUS_RESTARTED   => static::t('Restarted'),
            ASubscriptionPlan::STATUS_NOT_STARTED => static::t('Not started'),
            ASubscriptionPlan::STATUS_STOPPED     => static::t('Stopped'),
            ASubscriptionPlan::STATUS_FAILED      => static::t('Failed'),
            ASubscriptionPlan::STATUS_FINISHED    => static::t('Finished'),
        ];
    }
}
