<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * The "product" model class
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Model\Product
{
    /**
     * Flag if the object is set as snapshot for wishlist link
     *
     * @var boolean
     */
    protected $flagSnapshot = false;

    /**
     * Wishlist link id value.
     * Used only if object is created as wishlist link snapshot
     *
     * @var mixed
     */
    protected $wishlistLinkId = null;

    /**
     * "Wishlist links" to "parent product" links
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\MyWishlist\Model\WishlistLink", cascade={"persist", "merge"}, mappedBy="parentProduct")
     * @ORM\JoinColumn (name="wishlist_link_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $wishlistLinks;


    public function __construct(array $data = [])
    {
        $this->wishlistLinks = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get wishlistLinks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWishlistLinks()
    {
        return $this->wishlistLinks;
    }

    /**
     * Add wishlistLinks
     *
     * @param \XLite\Model\AModel $value
     * @return \XLite\Model\Product
     */
    public function addWishlistLinks($value)
    {
        $this->wishlistLinks[] = $value;
        return $this;
    }

    /**
     * Prepare attribute values
     *
     * @param array $ids Request-based selected attribute values OPTIONAL
     *
     * @return array
     */
    public function prepareAttributeValues($ids = [])
    {
        return $this->isSnapshotProduct() ? [] : parent::prepareAttributeValues($ids);
    }

    /**
     * Flag the object as snapshot
     */
    public function setSnapshotProduct()
    {
        $this->flagSnapshot = true;
    }

    /**
     * Check if the object is flagged as snapshot
     *
     * @return boolean
     */
    public function isSnapshotProduct()
    {
        return $this->flagSnapshot === true;
    }

    /**
     * Wishlist link id getter
     *
     * @return integer
     */
    public function getWishlistLinkId()
    {
        return $this->wishlistLinkId;
    }

    /**
     * Wishlist link id setter
     *
     * @param mixed $id
     *
     * @return void
     */
    public function setWishlistLinkId($id)
    {
        $this->wishlistLinkId = intval($id);
    }

    /**
     * Check - product has editable attrbiutes or not
     *
     * @return boolean
     */
    public function hasEditableAttributes()
    {
        return $this->isSnapshotProduct() ? false : parent::hasEditableAttributes();
    }

    /**
     * Check - product has editable attrbiutes or not
     *
     * @return boolean
     */
    public function hasMultipleAttributes()
    {
        return $this->isSnapshotProduct() ? false : parent::hasMultipleAttributes();
    }
}
