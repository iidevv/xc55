<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\Product;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table (name="brand_products",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"brand_id","product_id"})
 *      },
 *      indexes={
 *          @ORM\Index (name="orderby", columns={"orderby"})
 *      }
 * )
 */
class BrandProducts extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Product position in the brand
     *
     * @var integer
     *
     * @ORM\Column (type="integer", length=11, nullable=false)
     */
    protected $orderby = 0;

    /**
     * Relation to a brand entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ShopByBrand\Model\Brand", inversedBy="brandProducts")
     * @ORM\JoinColumn (name="brand_id", referencedColumnName="brand_id", onDelete="CASCADE")
     */
    protected $brand;

    /**
     * Relation to a product entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="categoryProducts")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

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
     * Set orderby
     *
     * @param integer $orderby
     * @return BrandProducts
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set category
     *
     * @param Brand $brand
     *
     * @return BrandProducts
     */
    public function setBrand(Brand $brand = null)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * Get category
     *
     * @return Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return BrandProducts
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
