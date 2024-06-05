<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Core;

use Qualiteam\SkinActFreeGifts\Model\FreeGift;
use Qualiteam\SkinActMain\Core\IAddProductValidator;
use XLite\Core\Database;
use XLite\Model\Cart;
use XLite\Model\Product;

class AddGiftValidator implements IAddProductValidator
{
    public function isValid(Product $product): bool
    {
        $this->checkAvailability($product);
        $this->checkGiftItemInCart(Cart::getInstance());
        $this->checkProductAgainstGiftTierInCart($product, Cart::getInstance());

        return true;
    }

    /**
     * @throws NonValidException
     */
    protected function checkAvailability(Product $product): void
    {
        if (!$product->isAvailable()) {
            throw new NonValidException('not available');
        }
    }

    /**
     * @throws NonValidException
     */
    protected function checkGiftItemInCart(Cart $cart): void
    {
        if ($cart->hasGiftItemAlready()) {
            throw new NonValidException('cart already has gift');
        }
    }

    /**
     * @throws NonValidException
     */
    protected function checkProductAgainstGiftTierInCart(Product $product, Cart $cart): void
    {
        /**
         * @var FreeGift $freeGiftTier
         */
        $freeGiftTier = $cart->getFreeGiftTier($product);

        if (
            $freeGiftTier
            && $freeGiftTier->getEnabled()
            && Database::getRepo(FreeGift::class)->hasProduct($freeGiftTier, $product)
        ) {
            return;
        }

        throw new NonValidException('product is not suitable as a gift for the cart');
    }
}
