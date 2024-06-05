<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class CleanURL extends \XLite\Model\CleanURL
{
    /**
     * Relation to a product entity
     *
     * @var \QSL\ShopByBrand\Model\Brand
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ShopByBrand\Model\Brand", inversedBy="cleanURLs")
     * @ORM\JoinColumn (name="brand_id", referencedColumnName="brand_id", onDelete="CASCADE")
     */
    protected $brand;

    /**
     * Get brand
     *
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set brand
     *
     * @param \QSL\ShopByBrand\Model\Brand $brand
     *
     * @return CleanURL
     */
    public function setBrand(\QSL\ShopByBrand\Model\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }
}
