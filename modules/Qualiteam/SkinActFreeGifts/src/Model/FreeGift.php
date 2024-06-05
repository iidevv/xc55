<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Free gift
 *
 * @ORM\Entity
 * @ORM\Table  (name="free_gifts")
 */
class FreeGift extends \XLite\Model\Base\I18n
{
    /**
     * Node unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $gift_tier_id;

    /**
     * Node status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Free Gift position parameter
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Gift tier min price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $tier_min_price = 0;

    /**
     * Gift tier max price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $tier_max_price = 0;

    /**
     * Free Gift items
     *
     * @var \Doctrine\Common\Collections\Collection|\Qualiteam\SkinActFreeGifts\Model\FreeGiftItem[]
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActFreeGifts\Model\FreeGiftItem", mappedBy="freeGift", cascade={"all"})
     */
    protected $items;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActFreeGifts\Model\FreeGiftTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return FreeGift
     */
    public function setEnabled($enabled)
    {
        $this->getPreviousState()->enabled = $this->enabled;
        $this->enabled                     = (bool)$enabled;

        return $this;
    }

    /**
     * Set Position
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Set Tier min price
     *
     * @param float $tier_min_price
     *
     * @return $this
     */
    public function setTierMinPrice($tier_min_price)
    {
        $this->tier_min_price = $tier_min_price;
        return $this;
    }

    /**
     * Set Tier max price
     *
     * @param float $tier_max_price
     *
     * @return $this
     */
    public function setTierMaxPrice($tier_max_price)
    {
        $this->tier_max_price = $tier_max_price;
        return $this;
    }

    /**
     * Add items
     *
     * @param \Qualiteam\SkinActFreeGifts\Model\FreeGiftItem $items
     * @return FreeGift
     */
    public function addItems(\Qualiteam\SkinActFreeGifts\Model\FreeGiftItem $items)
    {
        $this->items[] = $items;
        return $this;
    }

    /**
     * Get items
     *
     * @return \Qualiteam\SkinActFreeGifts\Model\FreeGiftItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getGiftTierId()
    {
        return $this->gift_tier_id;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Return Position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Return Tier min price
     *
     * @return float
     */
    public function getTierMinPrice()
    {
        return $this->tier_min_price;
    }

    /**
     * Return Tier max price
     *
     * @return float
     */
    public function getTierMaxPrice()
    {
        return $this->tier_max_price;
    }

    // {{{ Translation Getters / setters
    /**
     * @param string $tier_name
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setTierName($tier_name)
    {
        return $this->setTranslationField(__FUNCTION__, $tier_name);
    }

    /**
     * @return string
     */
    public function getTierName()
    {
        return $this->getTranslationField(__FUNCTION__);
    }
    // }}}
}
