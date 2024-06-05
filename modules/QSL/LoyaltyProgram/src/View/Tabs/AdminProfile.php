<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs on the User Profile page in the back-end.
 * @Extender\Mixin
 */
class AdminProfile extends \XLite\View\Tabs\AdminProfile
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'user_reward_points';

        return $list;
    }

    /**
     * Define page tabs.
     *
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if ($this->isRewardPointsTabVisible()) {
            $tabs['user_reward_points'] = [
                'weight'   => 300,
                'title'    => static::t('Reward points'),
                'template' => 'modules/QSL/LoyaltyProgram/profile/user_reward_points.twig',
            ];
        }

        return $tabs;
    }

    /**
     * Check if the "Reward points" tab is visible.
     *
     * @return bool
     */
    protected function isRewardPointsTabVisible()
    {
        return !$this->getProfile()->isAdmin();
    }
}
