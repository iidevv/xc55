<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Model\Image\ProductVariant;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product variant image
 *
 * @ORM\Entity
 * @ORM\Table  (name="product_variant_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Product variant
     *
     * @var \XC\ProductVariants\Model\ProductVariant
     *
     * @ORM\OneToOne   (targetEntity="XC\ProductVariants\Model\ProductVariant", inversedBy="image")
     * @ORM\JoinColumn (name="product_variant_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product_variant;

    /**
     * Alternative image text
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $alt = '';


    /**
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set product_variant
     *
     * @param \XC\ProductVariants\Model\ProductVariant $productVariant
     * @return Image
     */
    public function setProductVariant(\XC\ProductVariants\Model\ProductVariant $productVariant = null)
    {
        $this->product_variant = $productVariant;
        return $this;
    }

    /**
     * Get product_variant
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    public function getProductVariant()
    {
        return $this->product_variant;
    }
}
