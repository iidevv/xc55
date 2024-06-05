<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Form;

/**
 * Redeem Your Points form.
 */
class RedeemPoints extends \XLite\View\Form\AForm
{
    /**
     * Return default value for the "target" parameter.
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'reward_points';
    }

    /**
     * Return default value for the "action" parameter.
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'redeem';
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' use-inline-error');
    }
}
