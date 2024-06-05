<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Form;

/**
 * Adjust the shopper's reward points.
 */
class UserRewardPoints extends \XLite\View\Form\AForm
{
    /**
     * Get the default form target.
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'user_reward_points';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return array_merge(
            parent::getDefaultParams(),
            [
                'profile_id' => \XLite\Core\Request::getInstance()->profile_id,
            ]
        );
    }
}
