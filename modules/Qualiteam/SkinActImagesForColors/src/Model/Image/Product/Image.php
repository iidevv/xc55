<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\Model\Image\Product;


use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Image extends \XLite\Model\Image\Product\Image
{
    /**
     * @var \QSL\ColorSwatches\Model\Swatch
     *
     * @ORM\ManyToOne  (targetEntity="\QSL\ColorSwatches\Model\Swatch", inversedBy="productImages")
     * @ORM\JoinColumn (name="swatch_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $swatch;

    /**
     * @return \QSL\ColorSwatches\Model\Swatch
     */
    public function getSwatch()
    {
        return $this->swatch;
    }

    /**
     * @param \QSL\ColorSwatches\Model\Swatch $swatch
     */
    public function setSwatch($swatch)
    {
        $this->swatch = $swatch;
    }

}