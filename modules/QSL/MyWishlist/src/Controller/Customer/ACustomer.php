<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Wishlist products page controller
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Wishlist model cache
     *
     * @var mixed
     */
    protected $wishlist = null;

    /**
     * Wishlist defined from the request
     *
     * @return \QSL\MyWishlist\Model\Wishlist
     */
    public function getWishlist()
    {
        if (is_null($this->wishlist)) {
            $wishlist = \QSL\MyWishlist\Core\Wishlist::getInstance();

            $hash = \XLite\Core\Request::getInstance()->list_hash;
            if ($hash) {
                $this->wishlist = $wishlist->getWishlist(null, null, $hash);
            } else {
                $this->wishlist = $wishlist->getWishlist($this->getWishlistId());
            }
        }

        return $this->wishlist;
    }

    /**
     * Wishlist id is got from the request
     *
     * @return string
     */
    public function getWishlistId()
    {
        return \XLite\Core\Request::getInstance()->wishlist_id;
    }

    /**
     * Specific CSS styles for wishlist block
     *
     * @return string
     */
    public function getWishlistBlockCSS()
    {
        return 'wishlist';
    }

    public function isWishlistClose()
    {
        return false;
    }

    /**
     *
     * @param array $classes Classes array
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        if (\XLite\Core\Auth::getInstance()->isWishlistAvailable()) {
            $classes = array_flip($classes);
            unset($classes['unauthorized']);
            $classes = array_flip($classes);
        }

        return $classes;
    }

    /**
     * We allow to manage the wishlist only by customer who owns it
     *
     * @param \QSL\MyWishlist\Model\Wishlist $wishlist Wishlist model
     *
     * @return boolean
     */
    protected function isAllowToManageWishlist($wishlist)
    {
        $auth = \XLite\Core\Auth::getInstance();

        return $wishlist
            && $auth->isWishlistAvailable()
            && $wishlist->hasAccessToManage($auth->getProfile());
    }

    /**
     * @param \QSL\MyWishlist\Model\Wishlist $wishlist
     *
     * @return boolean
     */
    public function isAllowToShowWishlist()
    {
        $hash = \XLite\Core\Request::getInstance()->list_hash;
        $wishlist = $hash ? $this->getWishlist() : null;

        return $hash && $wishlist && $wishlist->getHash() === $hash;
    }

    /**
     * Check if the product is in the wishlist
     *
     * @param \XLite\Model\Product $product
     *
     * @return boolean
     */
    public function isProductInWishlist($product)
    {
        return $this->isProductIdInWishlist($product->getProductId());
    }

    /**
     * Check if the product id is in the wishlist
     *
     * @param int $productId
     *
     * @return boolean
     */
    public function isProductIdInWishlist($productId)
    {
        $wishlistProductIds = \QSL\MyWishlist\Core\Wishlist::getInstance()->getWishlistProductIds();

        return in_array($productId, $wishlistProductIds);
    }

    public function isWishlistLink()
    {
        return \XLite\Core\Request::getInstance()->mode === 'wishlist_link';
    }


    /**
     * Process the adding to wishlist
     *
     * @return void
     */
    protected function processWishlistAddProduct($product)
    {
        // Only owners are allowed to add the products to the wishlist
        $result = $this->isAllowToManageWishlist($this->getWishlist())
            ? $this->getWishlist()->addItem($product)
            : \QSL\MyWishlist\Model\Wishlist::FLAG_NOT_ADDED;

        $this->processWishlistMessage($result, $product);
    }

    /**
     *
     */
    public function movePostponedToRealWishlist()
    {
        $old = $this->suppressOutput;
        $postponed = \XLite\Core\Session::getInstance()->postponedWishlistProducts;
        if ($postponed && is_array($postponed)) {
            foreach ($postponed as $productId) {
                $this->addWishlistProductByProductId($productId);
            }

            \XLite\Core\Session::getInstance()->postponedWishlistProducts = [];
        }

        $this->setSuppressOutput($old);
    }

    /**
     * Define the top message defined by the result flag
     *
     * @param boolean $result Flag of adding procedure result
     *
     * @return void
     */
    protected function processWishlistMessage($result, $product)
    {
        switch ($result) {
            case \QSL\MyWishlist\Model\Wishlist::FLAG_ADDED:
                \XLite\Core\TopMessage::addInfo(
                    'You have added the product to your wishlist',
                    ['wishlist' => $this->buildURL('wishlist')]
                );
                \XLite\Core\Event::productAddedToWishlist([
                    'productid'     => $product->getProductId(),
                    'isNewAdded'    => true
                ]);
                break;

            case \QSL\MyWishlist\Model\Wishlist::FLAG_NOT_ADDED:
                \XLite\Core\TopMessage::addError('The product was not added to your wishlist. Try again');
                break;

            case \QSL\MyWishlist\Model\Wishlist::FLAG_ALREADY_ADDED:
                \XLite\Core\TopMessage::addInfo(
                    'The product is already added. We have moved it to the top of the wishlist',
                    ['wishlist' => $this->buildURL('wishlist')]
                );
                \XLite\Core\Event::productAddedToWishlist([
                    'productid'     => $product->getProductId(),
                    'isNewAdded'    => false
                ]);
        }

        if ($this->isAJAX()) {
            $this->setSuppressOutput(true);
        }
    }

    /**
     * The product model from request
     *
     * @return \XLite\Model\Product
     */
    protected function addWishlistProductByProductId($productId)
    {
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($productId);

        if ($product) {
            $this->processWishlistAddProduct($product);
        }
    }

    /**
     * Get wishlist products count
     *
     * @return integer
     */
    public function getWishlistProductsCount()
    {
        return \QSL\MyWishlist\Core\Wishlist::getInstance()->getWishlist() ?
            \QSL\MyWishlist\Core\Wishlist::getInstance()->getWishlist()->getProductsCount()
            : 0;
    }
}
