<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wishlist link to snapshot product item model
 *
 * @ORM\Entity
 * @ORM\Table  (name="wishlist_link")
 */
class WishlistLink extends \XLite\Model\AEntity
{
    /**
     * Wishlist link identificator
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Product position in the wishlist
     *
     * @var integer
     *
     * @ORM\Column (type="integer", length=11, nullable=false)
     */
    protected $orderby = 0;

    /**
     * Date of link creation
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $creationDate = 0;

    /**
     * Relation to a product snapshot entity
     *
     * @var \QSL\MyWishlist\Model\WishlistProductRecord
     *
     * @ORM\OneToOne (targetEntity="QSL\MyWishlist\Model\WishlistProductRecord", mappedBy="wishlistLink", cascade={"all"}, fetch="EAGER")
     */
    protected $snapshot;

    /**
     * Relation to a parent product entity
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="wishlistLinks", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="parent_product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $parentProduct;

    /**
     * Relation to a wishlist entity
     *
     * @var \QSL\MyWishlist\Model\Wishlist
     *
     * @ORM\ManyToOne (targetEntity="QSL\MyWishlist\Model\Wishlist")
     * @ORM\JoinColumn (name="wishlist_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $wishlist;


    public function getOrderby()
    {
        return $this->orderby;
    }

    public function setOrderby($value)
    {
        $this->orderby = $value;
        return $this;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setCreationDate($value)
    {
        $this->creationDate = $value;
        return $this;
    }

    public function getSnapshot()
    {
        return $this->snapshot;
    }

    public function setSnapshot($value)
    {
        $this->snapshot = $value;
        return $this;
    }

    public function getParentProduct()
    {
        return $this->parentProduct;
    }

    public function setParentProduct($value)
    {
        $this->parentProduct = $value;
        return $value;
    }

    public function getWishlist()
    {
        return $this->wishlist;
    }

    public function setWishlist($value)
    {
        $this->wishlist = $value;
        return $this;
    }

    /**
     * Check if the provided product is in this link
     *
     * @param \XLite\Model\Product $product
     *
     * @return boolean
     */
    public function hasProduct(\XLite\Model\Product $product)
    {
        return $this->getParentProduct() && $product->getProductId() === $this->getParentProduct()->getProductId();
    }

    /**
     * We copy title/description into the special wishlist product record
     *
     * @param \XLite\Model\Product $product
     *
     * @return void
     */
    public function createSnapshot(\XLite\Model\Product $product)
    {
        $this->setParentProduct($product);

        $this->setCreationDate(\XLite\Core\Converter::time());

        $this->setSnapshot(new \QSL\MyWishlist\Model\WishlistProductRecord(['wishlistLink' => $this]));

        $this->getSnapshot()->generateProductRecord($product);
    }

    /**
     * Return new product object with the name and
     *
     * @return \XLite\Model\Product
     */
    public function getSnapshotProduct()
    {
        return $this->getSnapshot()->getSnapshotProduct();
    }
}
