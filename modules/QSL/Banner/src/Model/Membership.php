<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Membership
 * @Extender\Mixin
 */
abstract class Membership extends \XLite\Model\Membership
{
    /**
     * Coupons
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="QSL\Banner\Model\Banner", mappedBy="memberships")
     */
    protected $banners;

    /**
     * Add coupons
     *
     * @param \QSL\Banner\Model\Banner $banners
     * @return Membership
     */
    public function addBanners(\QSL\Banner\Model\Banner $banners)
    {
        $this->banners[] = $banners;
        return $this;
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBanners()
    {
        return $this->banners;
    }
}
