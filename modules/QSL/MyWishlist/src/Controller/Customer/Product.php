<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Product page
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Customer\Product
{
    protected $wishlistLink = null;

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        if ($this->getProduct() && $this->getProduct()->isSnapshotProduct()) {
            $classes[] = 'snapshot-product';
        }

        return $classes;
    }

    /**
     * Define product
     *
     * @return \XLite\Model\Product
     */
    protected function defineProduct()
    {
        return $this->isWishlistLink() ? $this->defineSnapshotProduct() : parent::defineProduct();
    }

    public function defineSnapshotProduct()
    {
        return $this->getWishlistLink() ? $this->getWishlistLink()->getSnapshotProduct() : null;
    }

    protected function getWishlistLink()
    {
        if (is_null($this->wishlistLink)) {
            $this->wishlistLink = \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\WishlistLink')->find($this->getWishlistLinkId());
        }

        return $this->wishlistLink;
    }

    protected function getWishlistLinkId()
    {
        return \XLite\Core\Request::getInstance()->wishlist_link_id;
    }

    /**
     * Currently no action is required from the server-side
     *
     * @return void
     */
    protected function doActionAddFromWishlist()
    {
    }
}
