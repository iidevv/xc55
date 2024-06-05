<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\FormField\Select;

/**
 * Menu selector
 */
class CombineDiscounts extends \XLite\View\FormField\Select\Regular
{
    public const TYPE_SUM_UP = 'sum_up';
    public const TYPE_APPLY_MAX = 'apply_max';
    public const TYPE_APPLY_MIN = 'apply_min';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::TYPE_APPLY_MAX => static::t('Apply maximum discount'),
            static::TYPE_APPLY_MIN => static::t('Apply minimum discount'),
            static::TYPE_SUM_UP => static::t('Combine discounts'),
        ];
    }
}
