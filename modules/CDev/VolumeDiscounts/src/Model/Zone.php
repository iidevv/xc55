<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Zone extends \XLite\Model\Zone
{
    /**
     * Volume discounts
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="CDev\VolumeDiscounts\Model\VolumeDiscount", mappedBy="zones")
     */
    protected $volumeDiscounts;

    /**
     * Add volume discount
     *
     * @param \CDev\VolumeDiscounts\Model\VolumeDiscount $volumeDiscount
     * @return Zone
     */
    public function addVolumeDiscount(\CDev\VolumeDiscounts\Model\VolumeDiscount $volumeDiscount)
    {
        $this->volumeDiscounts[] = $volumeDiscount;
        return $this;
    }

    /**
     * Get volume discounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVolumeDiscounts()
    {
        return $this->volumeDiscounts;
    }
}
