<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CleanURL extends \XLite\Model\CleanURL
{
    /**
     * Relation to a product entity
     *
     * @var \CDev\Sale\Model\SaleDiscount
     *
     * @ORM\ManyToOne  (targetEntity="CDev\Sale\Model\SaleDiscount", inversedBy="cleanURLs")
     * @ORM\JoinColumn (name="sale_discount_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $sale_discount;

    /**
     * Set page
     *
     * @param \CDev\Sale\Model\SaleDiscount $saleDiscount
     * @return CleanURL
     */
    public function setSaleDiscount(\CDev\Sale\Model\SaleDiscount $saleDiscount = null)
    {
        $this->sale_discount = $saleDiscount;
        return $this;
    }

    /**
     * @return SaleDiscount
     */
    public function getSaleDiscount()
    {
        return $this->sale_discount;
    }
}
