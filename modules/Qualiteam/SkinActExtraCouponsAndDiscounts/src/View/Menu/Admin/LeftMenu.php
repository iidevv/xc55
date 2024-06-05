<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\View\Menu\Admin;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 * @Extender\After ("Qualiteam\SkinActProMembership")
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['extra_coupons_and_discounts'])) {
            $this->relatedTargets['extra_coupons_and_discounts'][] = 'extra_coupon';
        }

        parent::__construct($params);
    }

    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['pro_membership'][self::ITEM_CHILDREN]['extra_coupons_and_discounts'] = [
            static::ITEM_TITLE      => static::t('SkinActExtraCouponsAndDiscounts extra coupons and discounts'),
            static::ITEM_TARGET     => 'extra_coupons_and_discounts',
            static::ITEM_PERMISSION => 'manage catalog',
            static::ITEM_WEIGHT     => 300,
        ];

        return $items;
    }
}