<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Module\QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Event;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use QSL\MyWishlist\Model\Wishlist;

/**
 * @Extender\Depend ("QSL\MyWishlist")
 * @Extender\Mixin
 */
abstract class ACustomer extends \QSL\MyWishlist\Controller\Customer\ACustomer
{
    /**
     * Get top message after result process
     *
     * @param $result
     * @param $product
     *
     * @return void
     */
    protected function processWishlistMessage($result, $product)
    {
        if (Request::getInstance()->productIds) {
            if ($this->isProcessResultEqAnySuccessFlag($result)) {
                TopMessage::addInfo('SkinActFrequentlyBoughtTogether selected products are added to your wishlist');

                Event::productAddedToWishlist([
                    'productid'     => $product->getProductId(),
                    'isNewAdded'    => false
                ]);
            } else {
                TopMessage::addError('SkinActFrequentlyBoughtTogether the products was not added to your wishlist. Try again');
            }
        } else {
            parent::processWishlistMessage($result, $product);
        }
    }

    /**
     * Is process result eq any success status flag
     *
     * @param int $result
     *
     * @return bool
     */
    protected function isProcessResultEqAnySuccessFlag(int $result): bool
    {
        return $this->isResultEqAllowedFlag($result)
            || $this->isResultEqSuccessFlag($result);
    }

    /**
     * Is result has success flag
     *
     * @param int $result
     *
     * @return bool
     */
    protected function isResultEqSuccessFlag(int $result): bool
    {
        return $result === Wishlist::FLAG_ADDED;
    }

    /**
     * Is result has already added flag
     *
     * @param int $result
     *
     * @return bool
     */
    protected function isResultEqAllowedFlag(int $result): bool
    {
        return $result === Wishlist::FLAG_ALREADY_ADDED;
    }
}