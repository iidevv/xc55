<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Order
{
    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $mobileOrder = false;

    /**
     * @param boolean $mobileOrder
     *
     * @return Order
     */
    public function setMobileOrder($mobileOrder)
    {
        $this->mobileOrder = $mobileOrder;

        return $this;
    }

    /**
     *
     *
     * @return boolean
     */
    public function getMobileOrder()
    {
        return $this->mobileOrder;
    }
}
