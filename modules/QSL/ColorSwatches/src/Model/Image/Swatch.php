<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model\Image;

use Doctrine\ORM\Mapping as ORM;

/**
 * Swatch image
 *
 * @ORM\Entity
 * @ORM\Table  (name="qsl_color_swatch_image")
 */
class Swatch extends \XLite\Model\Base\Image
{
    /**
     * Relation to a swatch entity
     *
     * @var \QSL\ColorSwatches\Model\Swatch
     *
     * @ORM\OneToOne  (targetEntity="QSL\ColorSwatches\Model\Swatch", inversedBy="image")
     * @ORM\JoinColumn (name="swatch_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $swatch;

    /**
     * Set swatch
     *
     * @param \QSL\ColorSwatches\Model\Swatch $swatch
     * @return Swatch
     */
    public function setSwatch(\QSL\ColorSwatches\Model\Swatch $swatch = null)
    {
        $this->swatch = $swatch;
        return $this;
    }

    /**
     * Get swatch
     *
     * @return \QSL\ColorSwatches\Model\Swatch
     */
    public function getSwatch()
    {
        return $this->swatch;
    }
}
