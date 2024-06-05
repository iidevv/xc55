<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * Tabs related to user profile section
 * @Extender\Mixin
 */
abstract class AdminProfile extends \XLite\View\Tabs\AdminProfile
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'wishlist';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();
        if (Auth::getInstance()->isPermissionAllowed('[vendor] manage catalog')) {
            $tabs += [
                'wishlist' => [
                    'title'    => static::t('Wishlist'),
                    'template' => 'modules/QSL/MyWishlist/profile/wishlist.twig',
                    'weight'   => 1000,
                ]
            ];
        }

        return $tabs;
    }
}
