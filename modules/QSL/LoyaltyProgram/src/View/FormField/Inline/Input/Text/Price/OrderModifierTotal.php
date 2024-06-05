<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\FormField\Inline\Input\Text\Price;

use XCart\Extender\Mapping\Extender;

/**
 * Order surcharge widget for AOM
 * @Extender\Mixin
 */
class OrderModifierTotal extends \XLite\View\FormField\Inline\Input\Text\Price\OrderModifierTotal
{
    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param mixed $value Value
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        // When editing an order it doesn't start with the calculate() method,
        // so we have to reset the number of used reward points manually
        // in order to get it recalculated correctly.
        $this->getEntity()->resetNumberOfUsedRewardPoints();

        parent::saveFieldEntityValue($field, $value);
    }
}
