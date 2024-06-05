<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model\Image\Brand;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table  (name="brand_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Relation to the brand entity.
     *
     * @var \QSL\ShopByBrand\Model\Brand
     *
     * @ORM\OneToOne   (targetEntity="QSL\ShopByBrand\Model\Brand", inversedBy="image")
     * @ORM\JoinColumn (name="brand_id", referencedColumnName="brand_id", onDelete="CASCADE")
     */
    protected $brand;

    /**
     * The image "alt" attribute
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $alt = '';

    /**
     * return the image "alt" attribute
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set the image "alt" attribute
     *
     * @param string $alt New "alt" value
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set brand
     *
     * @param \QSL\ShopByBrand\Model\Brand $brand
     *
     * @return Image
     */
    public function setBrand(\QSL\ShopByBrand\Model\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }
}
