<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAmountForFreeShipping\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    /**
     * Amount for free shipping
     *
     * @var float
     *
     * @ORM\Column (type="decimal", nullable=true, precision=14, scale=4)
     */
    protected $amount_shipping = 0.00;

    /**
     * @return float
     */
    public function getAmountShipping()
    {
        return (float)$this->amount_shipping;
    }

    /**
     * @param float $amount_shipping
     */
    public function setAmountShipping(float $amount_shipping): void
    {
        $this->amount_shipping = $amount_shipping;
    }

    /**
     * Get category amount shipping
     *
     * @return float
     */
    public function getCategoryAmountShipping(): float
    {
        //var_dump([$this->getAmountShipping(), $this->getCategoryId()]);
        //
        if (
            $this->getAmountShipping() > 0
            || !$this->getParent()
        ) {
            return $this->getAmountShipping();
        }

        return $this->getParent()->getAmountShipping();
    }
}
