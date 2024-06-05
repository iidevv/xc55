<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Free gift item
 *
 * @ORM\Entity
 * @ORM\Table  (name="free_gift_items")
 */
class FreeGiftItem extends \XLite\Model\AEntity
{
    /**
     * Session cell name
     */
    public const SESSION_CELL_NAME = 'giftTierSearch';

    /**
     * Node unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $gift_id;

    /**
     * Free Gift (relation)
     *
     * @var \Qualiteam\SkinActFreeGifts\Model\FreeGift
     *
     * @ORM\ManyToOne  (targetEntity="Qualiteam\SkinActFreeGifts\Model\FreeGift", inversedBy="items")
     * @ORM\JoinColumn (name="gift_tier_id", referencedColumnName="gift_tier_id", onDelete="CASCADE")
     */
    protected $freeGift;

    /**
     * Product (relation)
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="giftTier")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @param FreeGift $freeGift
     */
    public function setFreeGift($freeGift)
    {
        $this->freeGift = $freeGift;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return FreeGiftItem
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getGiftId()
    {
        return (int) $this->gift_id;
    }

    /**
     * @return FreeGift
     */
    public function getFreeGift()
    {
        return $this->freeGift;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * SKU getter
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * Price getter
     *
     * @return double
     */
    public function getPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * Amount getter
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->getProduct()->getPublicAmount();
    }
}
