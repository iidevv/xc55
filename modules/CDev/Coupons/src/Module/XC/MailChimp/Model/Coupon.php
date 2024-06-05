<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Module\XC\MailChimp\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use CDev\Coupons\Module\XC\MailChimp\Core\Action\CouponUpdate;
use XC\MailChimp\Core\MailChimpECommerce;
use XC\MailChimp\Main;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MailChimp")
 */
class Coupon extends \CDev\Coupons\Model\Coupon
{
    /**
     * @ORM\PostPersist
     */
    public function mailChimpPostPersist()
    {
        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            foreach (Main::getMainStores() as $store) {
                MailChimpECommerce::getInstance()->createCoupon($store->getId(), $this);
            }
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function mailChimpPreUpdate()
    {
        $changeSet = \XLite\Core\Database::getEM()->getUnitOfWork()->getEntityChangeSet($this);

        if (
            Main::isMailChimpECommerceConfigured() && Main::getMainStores()
            && $this->getId()
            && array_filter($changeSet)
        ) {
            $action = new CouponUpdate($this);
            $action->execute();
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function mailChimpPreRemove()
    {
        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            foreach (Main::getMainStores() as $store) {
                MailChimpECommerce::getInstance()->removeCoupon(
                    $store->getId(),
                    $this->getId()
                );
            }
        }
    }
}
