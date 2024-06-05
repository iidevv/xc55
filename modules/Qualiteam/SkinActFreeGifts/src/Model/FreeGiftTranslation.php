<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Free Gift multilingual data
 *
 * @ORM\Entity
 * @ORM\Table  (name="free_gifts_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","gift_tier_id"}),
 *          @ORM\Index (name="gift_tier_id", columns={"gift_tier_id"})
 *      }
 * )
 */
class FreeGiftTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Gift tier name
     *
     * @var integer
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $tier_name = '';

    /**
     * @var \Qualiteam\SkinActFreeGifts\Model\FreeGift
     *
     * @ORM\ManyToOne (targetEntity="Qualiteam\SkinActFreeGifts\Model\FreeGift", inversedBy="translations")
     * @ORM\JoinColumn (name="gift_tier_id", referencedColumnName="gift_tier_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set tier name
     *
     * @param string $tier_name
     * @return FreeGiftTranslation
     */
    public function setTierName($tier_name)
    {
        $this->tier_name = $tier_name;
        return $this;
    }

    /**
     * Get tier name
     *
     * @return string
     */
    public function getTierName()
    {
        return $this->tier_name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return FreeGiftTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
