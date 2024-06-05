<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Button;

/**
 * Add to wishlist button
 */
class AddToWishlist extends \XLite\View\Button\Regular
{
    /**
     * Product in wishlist flag cache
     *
     * @var mixed
     */
    protected $isProductInWishlist = null;

    /**
     * Add JS controller functionality
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/MyWishlist/add_to_wishlist/controller.js';

        return $list;
    }

    /**
     * Add CSS style
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/MyWishlist/add_to_wishlist/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/MyWishlist/add_to_wishlist/body.twig';
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return $this->isProductInWishlist()
            ? 'Already in wishlist'
            : 'Add to wishlist';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'add_to_wishlist';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return 'return false;';
    }

    /**
     * We define login page as redirect when wishlist is not available
     *
     * @return string
     */
    protected function getLocationURL()
    {
        return $this->buildURL('login');
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass()
            . ' action add-to-wishlist'
            . ($this->isProductInWishlist() ? ' already-in-wishlist' : '')
            . (\XLite\Core\Auth::getInstance()->isLogged() ? '' : ' log-in')
            . (\XLite\Core\Auth::getInstance()->isWishlistAvailable() ? '' : ' must-be-logged-in');
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getDefaultAttributes()
    {
        return [
            'data-productid' => $this->getProductId(),
        ];
    }

    /**
     * Check if the product in the wishlist
     *
     * @return boolean
     */
    protected function isProductInWishlist()
    {
        if (is_null($this->isProductInWishlist)) {
            $wishlist = \QSL\MyWishlist\Core\Wishlist::getInstance()->getWishlist();

            $this->isProductInWishlist = $wishlist ? (bool)$wishlist->getWishlistLink($this->getProduct()) : false;
        }

        return $this->isProductInWishlist;
    }

    /**
     * The button is not visible on the snapshot product page
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->getProduct()->isSnapshotProduct();
    }
}
