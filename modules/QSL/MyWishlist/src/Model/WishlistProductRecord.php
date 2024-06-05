<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wishlist product record model
 *
 * @ORM\Entity
 * @ORM\Table  (name="wishlist_product_record")
 */
class WishlistProductRecord extends \XLite\Model\Base\I18n
{
    /**
     * Wishlist product record identificator
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Product price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $price = 0.0000;

    /**
     * Product SKU
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $sku;

    /**
     * Wishlist link entity
     *
     * @var \QSL\MyWishlist\Model\WishlistProductRecord
     *
     * @ORM\OneToOne  (targetEntity="QSL\MyWishlist\Model\WishlistLink", inversedBy="snapshot")
     * @ORM\JoinColumn (name="wishlist_link_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $wishlistLink;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\MyWishlist\Model\WishlistProductRecordTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($value)
    {
        $this->price = $value;
        return $this;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setSku($value)
    {
        $this->sku = $value;
    }

    public function getWishlistLink()
    {
        return $this->wishlistLink;
    }

    public function setWishlistLink($value)
    {
        $this->wishlistLink = $value;
        return $this;
    }

    /**
     * We copy the name and description translation object into our record
     *
     * @param \XLite\Model\Product $product
     *
     * @return void
     */
    public function generateProductRecord(\XLite\Model\Product $product)
    {
        $this->setSku($product->getSku());
        $this->setPrice($product->getPrice());

        foreach ($product->getTranslations() as $translation) {
            $this->addTranslations(new \QSL\MyWishlist\Model\WishlistProductRecordTranslation([
                'owner'         => $this,
                'name'          => $translation->getName(),
                'description'   => $translation->getDescription(),
                'code'          => $translation->getCode(),
            ]));
        }
    }

    /**
     * Return new product object with the name and
     *
     * @return \XLite\Model\Product
     */
    public function getSnapshotProduct()
    {
        $product = new \XLite\Model\Product();

        $product->setSku($this->getSku());
        $product->setPrice($this->getPrice());

        $product->setName($this->getTranslation()->getName());
        $product->setDescription($this->getTranslation()->getDescription());

        // We mark this object as a snapshot product
        $product->setSnapshotProduct();

        return $product;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $value
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($value)
    {
        return $this->setTranslationField(__FUNCTION__, $value);
    }

    // }}}
}
