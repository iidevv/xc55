<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Module\CDev\Coupons\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\Coupons", "XC\MailChimp"})
 */
class Coupon extends \CDev\Coupons\Model\Coupon
{
    /**
     * @ORM\PostPersist
     */
    public function mailChimpPostPersist()
    {
        if (!$this->isAbandonedCartCoupon()) {
            parent::mailChimpPostPersist();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function mailChimpPreUpdate()
    {
        if (!$this->isAbandonedCartCoupon()) {
            parent::mailChimpPreUpdate();
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function mailChimpPreRemove()
    {
        if (!$this->isAbandonedCartCoupon()) {
            parent::mailChimpPreRemove();
        }
    }
}
