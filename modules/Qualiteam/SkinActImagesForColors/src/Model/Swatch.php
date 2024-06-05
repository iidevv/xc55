<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Swatch extends \QSL\ColorSwatches\Model\Swatch
{

    /**
     * @var \XLite\Model\Image\Product\Image
     *
     * @ORM\OneToMany  (targetEntity="\XLite\Model\Image\Product\Image", mappedBy="swatch")
     */
    protected $productImages;

    /**
     * @return \XLite\Model\Image\Product\Image
     */
    public function getProductImages()
    {
        return $this->productImages;
    }

    /**
     * @param \XLite\Model\Image\Product\Image $productImages
     */
    public function setProductImages($productImages)
    {
        $this->productImages = $productImages;
    }

    /**
     * @param \XLite\Model\Image\Product\Image $productImages
     */
    public function addProductImages($productImages)
    {
        $this->productImages[] = $productImages;
    }

    public function __construct(array $data = [])
    {
        $this->productImages = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

}