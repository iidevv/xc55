<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Membership extends \XLite\Model\Membership
{

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Product", mappedBy="freeShippingForMemberships")
     *
     */
    protected $freeShippingProducts;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFreeShippingProducts()
    {
        return $this->freeShippingProducts;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $freeShippingProducts
     */
    public function setFreeShippingProducts($freeShippingProducts)
    {
        $this->freeShippingProducts = $freeShippingProducts;
    }

    public function addFreeShippingProduct($product)
    {
        $product->addFreeShippingForMemberships($this);
    }

    public function removeFreeShippingProduct($product)
    {
        $product->removeFreeShippingForMemberships($this);
    }


    public function __construct(array $data = [])
    {
        $this->freeShippingProducts = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

}