<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shopzilla category model.
 *
 * @ORM\Entity (repositoryClass="\QSL\ProductFeeds\Model\Repo\ShopzillaCategory")
 * @ORM\Table  (name="shopzilla_categories",
 *      indexes={
 *          @ORM\Index (name="name", columns={"name"})
 *      }
 * )
 */
class ShopzillaCategory extends \XLite\Model\AEntity
{
    /**
     * Shopzilla category identifier.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", unique=true, options={ "unsigned": true })
     */
    protected $shopzilla_id;

    /**
     * Category name.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Products added to the Shopzilla category.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Product", mappedBy="shopzillaCategory")
     */
    protected $products;

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
        return $this->getShopzillaId();
    }


    /**
     * Sets the entity identifier.
     *
     * @param integer $shopzillaId Identifier
     *
     * @return ShopzillaCategory
     */
    public function setShopzillaId($shopzillaId)
    {
        $this->shopzilla_id = $shopzillaId;

        return $this;
    }

    /**
     * Returns the entity identifier.
     *
     * @return integer
     */
    public function getShopzillaId()
    {
        return $this->shopzilla_id;
    }

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return ShopzillaCategory
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
     * Add product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return ShopzillaCategory
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
