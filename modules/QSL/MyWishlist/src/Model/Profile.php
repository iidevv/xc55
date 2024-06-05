<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * The "profile" model class
 * @Extender\Mixin
 */
abstract class Profile extends \XLite\Model\Profile
{
    /**
     * Wishlist relation
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="QSL\MyWishlist\Model\Wishlist", mappedBy="customer", cascade={"all"})
     */
    protected $wishlists;


    public function __construct(array $data = [])
    {
        $this->wishlists = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get wishlists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWishlists()
    {
        return $this->wishlists;
    }

    /**
     * Add wishlists
     *
     * @param \XLite\Model\AModel $value
     * @return \XLite\Model\Profile
     */
    public function addWishlists($value)
    {
        $this->wishlists[] = $value;
        return $this;
    }
}
