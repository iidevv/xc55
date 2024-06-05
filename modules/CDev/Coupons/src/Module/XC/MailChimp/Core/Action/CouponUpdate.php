<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Module\XC\MailChimp\Core\Action;

use XC\MailChimp\Core\Action\IMailChimpAction;
use XC\MailChimp\Core\MailChimpECommerce;
use XC\MailChimp\Main;

class CouponUpdate implements IMailChimpAction
{
    /**
     * @var \CDev\Coupons\Model\Coupon
     */
    private $coupon;

    /**
     * @inheritDoc
     */
    public function __construct(\CDev\Coupons\Model\Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     *
     */
    public function execute(): void
    {
        $ecCore = MailChimpECommerce::getInstance();

        foreach (Main::getMainStores() as $store) {
            $updateResult = $ecCore->updateCoupon($store->getId(), $this->coupon);
            if (!$updateResult) {
                $ecCore->createCoupon($store->getId(), $this->coupon);
            }
        }
    }
}
