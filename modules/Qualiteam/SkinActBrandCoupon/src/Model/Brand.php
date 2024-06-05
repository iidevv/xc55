<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBrandCoupon\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Brand extends \QSL\ShopByBrand\Model\Brand
{
    /**
     * Coupons
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="CDev\Coupons\Model\Coupon", mappedBy="brands")
     */
    protected $coupons;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->coupons = new ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add coupons
     *
     * @param \CDev\Coupons\Model\Coupon $coupons
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function addCoupons(\CDev\Coupons\Model\Coupon $coupons): \QSL\ShopByBrand\Model\Brand
    {
        $this->coupons[] = $coupons;
        return $this;
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getCoupons(): ArrayCollection|Collection
    {
        return $this->coupons;
    }
}