<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Gift tier (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActFreeGifts\Model\FreeGiftItem", mappedBy="product", cascade={"all"})
     */
    protected $giftTier;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->giftTier = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add giftTier
     *
     * @param \Qualiteam\SkinActFreeGifts\Model\FreeGift $giftTier
     * @return Product
     */
    public function addGiftTier(\Qualiteam\SkinActFreeGifts\Model\FreeGift $giftTier)
    {
        $this->giftTier[] = $giftTier;
        return $this;
    }

    /**
     * Get giftTier
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGiftTier()
    {
        return $this->giftTier;
    }

    public function getDisplayPrice()
    {
        return $this->isGiftMode() ? 0 : parent::getDisplayPrice();
    }

    protected function isGiftMode()
    {
        return Request::getInstance()->isAJAX()
            && Request::getInstance()->isFromGiftSource();
    }
}
