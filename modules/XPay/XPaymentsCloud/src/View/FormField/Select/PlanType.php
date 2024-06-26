<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\FormField\Select;

use XPay\XPaymentsCloud\Model\Subscription\Plan;

/**
 * Plan type selector
 */
class PlanType extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            Plan::TYPE_EACH  => static::t('Each'),
            Plan::TYPE_EVERY => static::t('Every'),
        ];
    }

}
