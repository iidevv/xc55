<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\FormField\Select;

/**
 * Form field to choose whether the points discount should be applied to the order total, or subtotal.
 */
class ApplyDiscountTo extends \XLite\View\FormField\Select\Regular
{
    /**
     * Modes
     */
    public const MODE_APPLY_TO_TOTAL    = 0;
    public const MODE_APPLY_TO_SUBTOTAL = 1;

    /**
     * Set value.
     *
     * @param mixed $value Value to set
     */
    public function setValue($value)
    {
        if (is_null($value)) {
            $value = self::MODE_APPLY_TO_TOTAL;
        }

        parent::setValue($value);
    }

    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            self::MODE_APPLY_TO_TOTAL    => static::t('Apply reward discount: Total'),
            self::MODE_APPLY_TO_SUBTOTAL => static::t('Apply reward discount: Subtotal'),
        ];
    }
}
