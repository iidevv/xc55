<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFullWidthImages\Model\Image\Product;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\Product;

/**
 * Product image
 *
 * @ORM\Entity
 * @ORM\Table  (name="product_full_width_images")
 */
class FullWidthImage extends \XLite\Model\Base\Image
{
    /**
     * Image position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="full_width_images")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Alternative image text
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $alt = '';

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
     * Set orderby
     *
     * @param integer $orderby
     *
     * @return \Qualiteam\SkinActFullWidthImages\Model\Image\Product\FullWidthImage
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;

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
     * Set alt
     *
     * @param string $alt
     *
     * @return FullWidthImage
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     *
     * @return FullWidthImage
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;

        return $this;
    }
}