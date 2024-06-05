<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\View\Tabs;

use Qualiteam\SkinActProMembership\Helpers\Profile;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'extra_coupons_and_discounts';

        return $list;
    }
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if ((new Profile)->isProfileProMembership()) {
            $tabs['extra_coupons_and_discounts'] = [
                'title'    => static::t('SkinActExtraCouponsAndDiscounts extra coupons and discounts'),
                'template' => 'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/page/extra_coupons_and_discounts.twig',
                'weight'   => 19500,
            ];
        }

        return $tabs;
    }

    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/extra_classes.js';

        return $list;
    }
}