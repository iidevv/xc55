<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Module\CDev\Coupons\View\Panel;

use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Repo\ARepo;
use QSL\AbandonedCartReminder\Model\Repo\Coupon as Repo;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class Coupons extends \CDev\Coupons\View\StickyPanel\Coupon\Admin\Coupons
{
    /**
     * Define additional button widgets.
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $count = Database::getRepo('CDev\Coupons\Model\Coupon')->search(
            new CommonCell([Repo::SEARCH_EXPIRED => true]),
            ARepo::SEARCH_MODE_COUNT
        );
        if ($count) {
            $list['deleteExpiredCoupons'] = [
                'class'    => 'QSL\AbandonedCartReminder\View\Button\DeleteExpiredCoupons',
                'params'   => [
                    'disabled'   => false,
                    'style'      => 'more-action',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 100,
            ];
        }

        return $list;
    }
}
