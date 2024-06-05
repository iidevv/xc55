<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\Product as ProductEntity;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table (name="sale_discount_products",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"sale_id","product_id"})
 *      },
 * )
 */
class SaleDiscountProduct extends \XLite\Model\AEntity
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
     * Relation to a sale entity
     *
     * @var \CDev\Sale\Model\SaleDiscount
     *
     * @ORM\ManyToOne  (targetEntity="CDev\Sale\Model\SaleDiscount", inversedBy="saleDiscountProducts")
     * @ORM\JoinColumn (name="sale_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $saleDiscount;

    /**
     * Relation to a product entity
     *
     * @var ProductEntity
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="saleDiscountProducts")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaleDiscount(): ?SaleDiscount
    {
        return $this->saleDiscount;
    }

    public function setSaleDiscount(SaleDiscount $sale): SaleDiscountProduct
    {
        $this->saleDiscount = $sale;

        return $this;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(ProductEntity $product): SaleDiscountProduct
    {
        $this->product = $product;

        return $this;
    }

    public function getProductId(): int
    {
        return $this->getProduct()->getProductId();
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * @return float
     */
    public function getProductPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * @return int
     */
    public function getProductAmount()
    {
        return $this->getProduct()->getAmount();
    }
}
