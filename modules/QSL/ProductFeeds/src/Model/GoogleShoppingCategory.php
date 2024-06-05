<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoogleShoppingCategory category model.
 *
 * @ORM\Entity (repositoryClass="\QSL\ProductFeeds\Model\Repo\GoogleShoppingCategory")
 * @ORM\Table  (name="google_shopping_categories",
 *      indexes={
 *          @ORM\Index (name="name", columns={"name"})
 *      }
 * )
 */
class GoogleShoppingCategory extends \XLite\Model\AEntity
{
    /**
     * GoogleShopping category identifier.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", unique=true, options={ "unsigned": true })
     */
    protected $google_id;

    /**
     * Category name.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Products added to the GoogleShopping category.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Product", mappedBy="googleShoppingCategory")
     */
    protected $products;

    /**
     * Whether the category is deprecated, or not.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $deprecated = false;

    /**
     * Constructor.
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }
    /**
     * Get object unique id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getGoogleId();
    }

    /**
     * Set the entity identifier.
     *
     * @param integer $googleId Identifier
     *
     * @return GoogleShoppingCategory
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get the entity identifier.
     *
     * @return integer
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return GoogleShoppingCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the "deprecated" flag.
     *
     * @param boolean $deprecated Flag
     *
     * @return GoogleShoppingCategory
     */
    public function setDeprecated($deprecated)
    {
        $this->deprecated = $deprecated;
        return $this;
    }

    /**
     * Returns the "deprecated" flag.
     *
     * @return boolean
     */
    public function getDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * Add product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return GoogleShoppingCategory
     */
    public function addProducts(\XLite\Model\Product $product)
    {
        $this->products[] = $product;
        return $this;
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
