<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product statistics model (for 'Customers who viewed this product bought' widget)
 *
 * @ORM\Entity
 * @ORM\Table  (name="product_stats")
 */
class ProductStats extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var   integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $stat_id;

    /**
     * Viewed product
     *
     * @var   \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="views_stats")
     * @ORM\JoinColumn (name="viewed_product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $viewed_product;

    /**
     * Bought product
     *
     * @var   \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="purchase_stats")
     * @ORM\JoinColumn (name="bought_product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $bought_product;

    /**
     * Count of bought products
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $count = 1;

    /**
     * Get stat_id
     *
     * @return integer
     */
    public function getStatId()
    {
        return $this->stat_id;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return ProductStats
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set viewed_product
     *
     * @param \XLite\Model\Product $viewedProduct
     * @return ProductStats
     */
    public function setViewedProduct(\XLite\Model\Product $viewedProduct = null)
    {
        $this->viewed_product = $viewedProduct;
        return $this;
    }

    /**
     * Get viewed_product
     *
     * @return \XLite\Model\Product
     */
    public function getViewedProduct()
    {
        return $this->viewed_product;
    }

    /**
     * Set bought_product
     *
     * @param \XLite\Model\Product $boughtProduct
     * @return ProductStats
     */
    public function setBoughtProduct(\XLite\Model\Product $boughtProduct = null)
    {
        $this->bought_product = $boughtProduct;
        return $this;
    }

    /**
     * Get bought_product
     *
     * @return \XLite\Model\Product
     */
    public function getBoughtProduct()
    {
        return $this->bought_product;
    }
}
