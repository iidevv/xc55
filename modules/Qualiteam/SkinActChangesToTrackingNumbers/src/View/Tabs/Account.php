<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActChangesToTrackingNumbers\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'parcels';

        return $list;
    }

    protected function defineTabs()
    {
        return parent::defineTabs()
            + [
                'parcels' => [
                    'title'    => static::t('SkinActChangesToTrackingNumbers parcels'),
                    'template' => 'modules/Qualiteam/SkinActChangesToTrackingNumbers/parcels.twig',
                    'weight'   => 500,
                ]
            ];
    }

}