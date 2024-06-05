<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Module\XC\MultiVendor\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * The "profile" model class
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Stripe AccountID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $stripeSellerAccountId = '';

    /**
     * @return string
     */
    public function getStripeSellerAccountId()
    {
        return $this->stripeSellerAccountId;
    }

    /**
     * @param string $stripeSellerAccountId
     */
    public function setStripeSellerAccountId($stripeSellerAccountId)
    {
        $this->stripeSellerAccountId = $stripeSellerAccountId;
    }
}
