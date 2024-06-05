<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * The "profile" model class
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Stripe CustomerID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $stripeCustomerId = '';

    /**
     * @return string
     */
    public function getStripeCustomerId()
    {
        return $this->stripeCustomerId;
    }

    /**
     * @param string $stripeCustomerId
     */
    public function setStripeCustomerId($stripeCustomerId)
    {
        $this->stripeCustomerId = $stripeCustomerId;
    }
}
