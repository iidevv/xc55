<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs for the User Account page.
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'reward_points';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        $profile = $this->getProfile();
        if ($this->isLogged() && $profile && $profile->isLoyaltyProgramEnabled()) {
            $tabs['reward_points'] = [
                'weight'   => 350,
                'title'    => static::t('Reward points'),
                'template' => 'modules/QSL/LoyaltyProgram/account/reward_points_tab.twig',
            ];
        }

        return $tabs;
    }
}
