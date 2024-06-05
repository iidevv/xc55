<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model\Image\SpecialOffer;

use Doctrine\ORM\Mapping as ORM;

/**
 * Special offer image.
 *
 * @ORM\Entity
 * @ORM\Table  (name="special_offer_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Relation to the brand entity.
     *
     * @var \QSL\SpecialOffersBase\Model\SpecialOffer
     *
     * @ORM\OneToOne   (targetEntity="QSL\SpecialOffersBase\Model\SpecialOffer", inversedBy="image", cascade={"persist"})
     * @ORM\JoinColumn (name="offer_id", referencedColumnName="offer_id")
     */
    protected $specialOffer;

    /**
     * Set specialOffer
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $specialOffer
     * @return Image
     */
    public function setSpecialOffer(\QSL\SpecialOffersBase\Model\SpecialOffer $specialOffer = null)
    {
        $this->specialOffer = $specialOffer;
        return $this;
    }

    /**
     * Get specialOffer
     *
     * @return \QSL\SpecialOffersBase\Model\SpecialOffer
     */
    public function getSpecialOffer()
    {
        return $this->specialOffer;
    }
}
