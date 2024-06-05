<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Category;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category quick flags
 *
 * @ORM\Entity
 * @ORM\Table  (name="category_quick_flags")
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
     * @var \XLite\Model\Category
     *
     * @ORM\OneToOne   (targetEntity="XLite\Model\Category", inversedBy="quickFlags")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
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
     * @param \XLite\Model\Category $category
     * @return QuickFlags
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
