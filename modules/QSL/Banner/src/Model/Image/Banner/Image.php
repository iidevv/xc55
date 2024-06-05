<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model\Image\Banner;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banner image
 *
 * @ORM\Entity (repositoryClass="\QSL\Banner\Model\Repo\Image\Banner\Image")
 * @ORM\Table  (name="banner_slide_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Relation to a product entity
     *
     * @var   \QSL\Banner\Model\BannerSlide
     *
     * @ORM\OneToOne  (targetEntity="QSL\Banner\Model\BannerSlide", inversedBy="image")
     * @ORM\JoinColumn (name="banner_slide_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $bannerSlide;


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
     * Set product
     *
     * @param \QSL\Banner\Model\BannerSlide $bannerSlide
     * @return Image
     */
    public function setBannerSlide(\QSL\Banner\Model\BannerSlide $bannerSlide = null)
    {
        $this->bannerSlide = $bannerSlide;
        return $this;
    }

    /**
     * Get product
     *
     * @return \QSL\Banner\Model\BannerSlide
     */
    public function getBannerSlide()
    {
        return $this->bannerSlide;
    }

    public function getImage()
    {
        return $this;
    }
}
