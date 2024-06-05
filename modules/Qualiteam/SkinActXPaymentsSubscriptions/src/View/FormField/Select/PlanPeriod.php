<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Select;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use XLite\Model\WidgetParam\TypeString;
use XLite\View\FormField\Select\ASelect;

/**
 * Plan period selector
 */
class PlanPeriod extends ASelect
{
    const PARAM_PLAN_TYPE = "planType";

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ASubscriptionPlan::TYPE_EACH  => [
                ASubscriptionPlan::PERIOD_WEEK  => static::t('xps.week'),
                ASubscriptionPlan::PERIOD_MONTH => static::t('xps.month'),
                ASubscriptionPlan::PERIOD_YEAR  => static::t('xps.year'),
            ],
            ASubscriptionPlan::TYPE_EVERY => [
                ASubscriptionPlan::PERIOD_DAY   => static::t('xps.days'),
                ASubscriptionPlan::PERIOD_WEEK  => static::t('xps.weeks'),
                ASubscriptionPlan::PERIOD_MONTH => static::t('xps.months'),
                ASubscriptionPlan::PERIOD_YEAR  => static::t('xps.years'),
            ],
        ];
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PLAN_TYPE => new TypeString('Plan type', ASubscriptionPlan::TYPE_EACH),
        ];
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return $options[$this->getParam(static::PARAM_PLAN_TYPE)];
    }
}
