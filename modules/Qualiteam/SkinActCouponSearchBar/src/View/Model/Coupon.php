<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCouponSearchBar\View\Model;


use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Repo\ARepo;
use XLite\View\Button\AButton;
use XLite\View\Button\Link;
use XLite\Model\Order;

/**
 * @Extender\Mixin
 */
class Coupon extends \CDev\Coupons\View\Model\Coupon
{
    protected function getFormButtons()
    {
        $buttons = parent::getFormButtons();

        $coupon = $this->getDefaultModelObject();

        if ($coupon->isPersistent()) {

            $cnd = new CommonCell();
            $cnd->coupon_id = $coupon->getId();

            $ordersCount = Database::getRepo(Order::class)
                ->search($cnd, ARepo::SEARCH_MODE_COUNT);

            if ($ordersCount > 0) {

                $link = $this->buildURL('order_list', '', ['couponId' => $coupon->getId()]);

                $buttons['orders'] = new Link(
                    [
                        AButton::PARAM_LABEL => static::t('SkinActCouponSearchBar Orders button'),
                        AButton::PARAM_BTN_TYPE => 'regular-main-button always-enabled',
                        AButton::PARAM_STYLE => 'action',
                        Link::PARAM_LOCATION => $link,
                    ]
                );
            }
        }

        return $buttons;
    }
}

