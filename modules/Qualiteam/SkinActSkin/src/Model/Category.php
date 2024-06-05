<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Model;

use Qualiteam\SkinActSkin\Model\Image\Category\Image2;
use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;


/**
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true, options={"default":false} )
     */
    protected $showOnHomePage = false;

    /**
     * @return bool
     */
    public function getShowOnHomePage()
    {
        return $this->showOnHomePage;
    }

    /**
     * @param bool $showOnHomePage
     */
    public function setShowOnHomePage($showOnHomePage)
    {
        $this->showOnHomePage = $showOnHomePage;
    }

    /**
     * Category title color
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1, options={"default" : "W"})
     */
    protected $color = 'W';

    /**
     * @return bool
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * Color
     *
     * @var string
     *
     * @ORM\Column (type="string", length=6, nullable=true)
     */
    protected $bgColor;

    /**
     * @return bool
     */
    public function getBgColor()
    {
        return $this->bgColor;
    }

    /**
     * @param string $bgColor
     */
    public function setBgColor($bgColor)
    {
        $this->bgColor = $bgColor;
    }

    /**
     * One-to-one relation with category_images table
     *
     * @var Image2
     *
     * @ORM\OneToOne  (targetEntity="Qualiteam\SkinActSkin\Model\Image\Category\Image2", mappedBy="category", cascade={"all"})
     */
    protected $image2;

    /**
     * Get image
     *
     * @return \Qualiteam\SkinActSkin\Model\Image\Category\Image2
     */
    public function getImage2()
    {
        return $this->image2;
    }

    /**
     * Set image
     *
     * @param \Qualiteam\SkinActSkin\Model\Image\Category\Image2 $image2 Image2 OPTIONAL
     *
     * @return void
     */
    public function setImage2(\Qualiteam\SkinActSkin\Model\Image\Category\Image2 $image2 = null)
    {
        $this->image2 = $image2;
    }

    /**
     * Check if category has image
     *
     * @return boolean
     */
    public function hasImage2()
    {
        return $this->getImage2() !== null;
    }

}
