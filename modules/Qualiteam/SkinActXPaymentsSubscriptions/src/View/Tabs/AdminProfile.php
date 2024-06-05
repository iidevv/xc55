<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs related to user profile section
 *
 * @Extender\Mixin
 */
abstract class AdminProfile extends \XLite\View\Tabs\AdminProfile
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'x_payments_user_subscription';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        $profile = $this->getProfile();

        if ($profile
            && $profile->hasSubscriptions()
        ) {
            $tabs['x_payments_user_subscription'] = [
                'weight'   => 900,
                'title'    => static::t('Subscriptions'),
                'template' => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/profile/subscription.twig',
            ];
        }

        return $tabs;
    }
}
