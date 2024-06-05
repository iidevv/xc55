<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Product SKU
     *
     * @var string
     *
     * @ORM\Column (type="string", length=25, nullable=true)
     */
    protected $avaTaxCode;

    /**
     * Set avaTaxCode
     *
     * @param string $avaTaxCode
     * @return Product
     */
    public function setAvaTaxCode($avaTaxCode)
    {
        $this->avaTaxCode = $avaTaxCode;
        return $this;
    }

    /**
     * Get avaTaxCode
     *
     * @return string
     */
    public function getAvaTaxCode()
    {
        return $this->avaTaxCode;
    }
}
