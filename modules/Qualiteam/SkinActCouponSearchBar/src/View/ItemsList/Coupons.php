<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCouponSearchBar\View\ItemsList;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Coupons extends \CDev\Coupons\View\ItemsList\Coupons
{

    protected $sortByModes = [
        'code1' => 'Coupon code',
    ];


    protected function defineColumns()
    {
        $cols = parent::defineColumns();
        $cols['code'][static::COLUMN_SORT] = 'code1';
        $cols['code'][static::COLUMN_HEAD_TEMPLATE] = 'modules/Qualiteam/SkinActCouponSearchBar/head.cell.twig';

        $cols['uses_count']['template'] = 'modules/Qualiteam/SkinActCouponSearchBar/uses_count.twig';

        return $cols;
    }

    protected function getSearchPanelClass()
    {
        return '\Qualiteam\SkinActCouponSearchBar\View\SearchPanel\Coupons';
    }

    public static function getSearchParams()
    {
        return [
            'substr' => 'substr',
        ];
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActCouponSearchBar/Coupons.css';

        return $list;
    }

}