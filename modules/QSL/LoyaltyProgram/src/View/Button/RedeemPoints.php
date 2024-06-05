<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Button;

/**
 * "Redeem points" button.
 */
class RedeemPoints extends \XLite\View\Button\Submit
{
    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Redeem';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . ' redeem-points');
    }
}
