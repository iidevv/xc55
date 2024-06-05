<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Select;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use XLite\View\FormField\Select\ASelect;

/**
 * Plan type selector
 */
class PlanType extends ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ASubscriptionPlan::TYPE_EACH  => static::t('Each'),
            ASubscriptionPlan::TYPE_EVERY => static::t('Every'),
        ];
    }
}
