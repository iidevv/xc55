<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class ProfileWithCoupons extends \XLite\Model\Profile
{
    /**
     * Number of reminder coupons generated for the user.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true, "default": 0 })
     */
    protected $numberOfReminderCoupons = 0;

    /**
     * Updates the number of coupons sent for abandoned carts to the user.
     *
     * @param integer $numberOfReminderCoupons New number of coupons
     *
     * @return ProfileWithCoupons
     */
    public function setNumberOfReminderCoupons($numberOfReminderCoupons)
    {
        $this->numberOfReminderCoupons = $numberOfReminderCoupons;

        return $this;
    }

    /**
     * Returns the number of coupons sent for abandoned carts to the user.
     *
     * @return integer
     */
    public function getNumberOfReminderCoupons()
    {
        return $this->numberOfReminderCoupons;
    }
}
