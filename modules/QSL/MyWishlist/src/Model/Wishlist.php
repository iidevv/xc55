<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wishlist item model
 *
 * @ORM\Entity
 * @ORM\Table  (name="wishlists")
 */
class Wishlist extends \XLite\Model\AEntity
{
    public const FLAG_ALREADY_ADDED    = 1;
    public const FLAG_ADDED            = 2;
    public const FLAG_NOT_ADDED        = 3;

    /**
     * Wishlist identificator
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Customer profile of wishlist
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Profile", inversedBy="wishlists")
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $customer;

    /**
     * Wishlist name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $wishlistName = '';

    /**
     * Cell hash
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $hash;

    /**
     * Wishlist links relation
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="QSL\MyWishlist\Model\WishlistLink", mappedBy="wishlist", cascade={"all"})
     */
    protected $wishlistLinks;


    public function __construct(array $data = [])
    {
        $this->wishlistLinks = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newList = parent::cloneEntity();
        $newList->setCustomer($this->getCustomer());

        foreach ($this->getWishlistLinks() as $link) {
            $cloned = $link->cloneEntity();
            $newList->addWishlistLinks($cloned);
            $cloned->setParentProduct($link->getParentProduct());
            $cloned->setWishlist($newList);
        }

        return $newList;
    }

    public function setCustomer($value)
    {
        $this->customer = $value;
        return $this;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setWishlistName($value)
    {
        $this->wishlistName = $value;
        return $this;
    }

    public function getWishlistName()
    {
        return $this->wishlistName;
    }

    /**
     * @param $hash
     *
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function generateHash()
    {
        $hash = substr(hash('md5', uniqid('', true)), 0, 7);
        $this->setHash($hash);

        return $hash;
    }

    public function addWishlistLinks($link)
    {
        $this->wishlistLinks[] = $link;
        return $this;
    }

    public function getWishlistLinks()
    {
        return $this->wishlistLinks;
    }

    /**
     * Only the author of wishlist has access to it
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return boolean
     */
    public function hasAccessToManage(\XLite\Model\Profile $profile)
    {
        return $profile->getProfileId() === $this->getCustomer()->getProfileId();
    }

    /**
     * Process adding product to wishlist
     *
     * @param \XLite\Model\Product $product
     *
     * @return mixed
     */
    public function addItem(\XLite\Model\Product $product, $orderby = 0)
    {
        $repo = \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\WishlistLink');

        $result = static::FLAG_NOT_ADDED;

        $link = $this->getWishlistLink($product);

        if ($link) {
            // The link is already here and we move the product to the top of wishlist
            $link->setOrderby($repo->getMinimumOrderby($this) - 1);

            $result = static::FLAG_ALREADY_ADDED;
        } else {
            $link = new \QSL\MyWishlist\Model\WishlistLink(['wishlist' => $this]);

            \XLite\Core\Database::getEM()->persist($link);
            \XLite\Core\Database::getEM()->flush($link);

            $link->createSnapshot($product);

            \XLite\Core\Database::getEM()->persist($link);

            $this->addWishlistLinks($link);

            $result = static::FLAG_ADDED;
        }

        \XLite\Core\Database::getEM()->flush();

        return $result;
    }

    /**
     * Remove wishlist item defined via snapshot product
     *
     * @param integer $linkId Wishlist link id
     *
     * @return void
     */
    public function removeWishlistLink($linkId)
    {
        $repo = \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\WishlistLink');

        $link = $repo->find($linkId);

        if ($link) {
            $repo->delete($link);
        }
    }

    /**
     * Check if current wishlist has any wishlist links in it
     *
     * @return boolean
     */
    public function hasProducts()
    {
        return $this->getWishlistLinks()->count() > 0;
    }

    /**
     * Check if current wishlist has any wishlist links in it
     *
     * @return integer
     */
    public function getProductsCount()
    {
        $result = 0;

        foreach ($this->getWishlistLinks() as $link) {
            $product = $link->getParentProduct();
            if ($product && $product->getEnabled()) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * Check if current wishlist has visible products
     *
     * @return boolean
     */
    public function hasVisibleProducts()
    {
        $result = false;

        foreach ($this->getWishlistLinks() as $link) {
            if ($link->getParentProduct()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check if the wishlist link is present in wishlist via its snapshot and return it
     *
     * @param \XLite\Model\Product $product Snapshot product model
     *
     * @return \QSL\MyWishlist\Model\WishlistLink|null
     */
    public function getWishlistLink(\XLite\Model\Product $product)
    {
        $result = null;

        foreach ($this->getWishlistLinks() as $link) {
            if ($link->hasProduct($product)) {
                $result = $link;
                break;
            }
        }

        return $result;
    }
}
