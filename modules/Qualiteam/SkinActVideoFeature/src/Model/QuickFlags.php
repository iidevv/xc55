<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category quick flags
 *
 * @ORM\Entity
 * @ORM\Table  (name="video_category_quick_flags")
 */
class QuickFlags extends \XLite\Model\AEntity
{
    /**
     * Doctrine ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Total number of subcategories
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $subcategories_count_all = 0;

    /**
     * Number of enabled subcategories
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $subcategories_count_enabled = 0;

    /**
     * Relation to a category entity
     *
     * @var \Qualiteam\SkinActVideoFeature\Model\VideoCategory
     *
     * @ORM\OneToOne   (targetEntity="Qualiteam\SkinActVideoFeature\Model\VideoCategory", inversedBy="quickFlags")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subcategories_count_all
     *
     * @param integer $subcategoriesCountAll
     * @return QuickFlags
     */
    public function setSubcategoriesCountAll($subcategoriesCountAll)
    {
        $this->subcategories_count_all = $subcategoriesCountAll;
        return $this;
    }

    /**
     * Get subcategories_count_all
     *
     * @return integer
     */
    public function getSubcategoriesCountAll()
    {
        return $this->subcategories_count_all;
    }

    /**
     * Set subcategories_count_enabled
     *
     * @param integer $subcategoriesCountEnabled
     * @return QuickFlags
     */
    public function setSubcategoriesCountEnabled($subcategoriesCountEnabled)
    {
        $this->subcategories_count_enabled = $subcategoriesCountEnabled;
        return $this;
    }

    /**
     * Get subcategories_count_enabled
     *
     * @return integer
     */
    public function getSubcategoriesCountEnabled()
    {
        return $this->subcategories_count_enabled;
    }

    /**
     * Set category
     *
     * @param \Qualiteam\SkinActVideoFeature\Model\VideoCategory $category
     * @return QuickFlags
     */
    public function setCategory(\Qualiteam\SkinActVideoFeature\Model\VideoCategory $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\VideoCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
}