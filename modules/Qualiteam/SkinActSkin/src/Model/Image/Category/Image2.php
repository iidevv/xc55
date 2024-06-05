<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Model\Image\Category;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table  (name="category_images2")
 */
class Image2 extends \XLite\Model\Base\Image
{
    /**
     * Relation to a category entity
     *
     * @var \XLite\Model\Category
     *
     * @ORM\OneToOne   (targetEntity="XLite\Model\Category", inversedBy="image2")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
     */
    protected $category;

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
     * Set category
     *
     * @param \XLite\Model\Category $category
     * @return Image
     */
    public function setCategory(\XLite\Model\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \XLite\Model\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
