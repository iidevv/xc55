<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table  (name="video_category_images")
*/
class Image extends \XLite\Model\Base\Image
{
    /**
     * Relation to a category entity
     *
     * @var VideoCategory
     *
     * @ORM\OneToOne   (targetEntity="Qualiteam\SkinActVideoFeature\Model\VideoCategory", inversedBy="image")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="id", onDelete="CASCADE")
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
     * @param VideoCategory $category
     *
     * @return Image
     */
    public function setCategory(VideoCategory $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return VideoCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
}