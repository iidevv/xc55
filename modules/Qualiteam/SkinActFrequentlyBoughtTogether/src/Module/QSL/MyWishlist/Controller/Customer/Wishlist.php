<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Module\QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\Product;

/**
 * @Extender\Depend ("QSL\MyWishlist")
 * @Extender\Mixin
 */
class Wishlist extends \QSL\MyWishlist\Controller\Customer\Wishlist
{

    /**
     * Add item(s) to wishlist
     *
     * @return void
     */
    protected function doActionAddToWishlist()
    {
        $productIds = \XLite\Core\Request::getInstance()->productIds;

        if (!empty($productIds)) {
            $products = Database::getRepo(Product::class)
                ->findByIds($productIds);

            foreach ($products as $product) {
                $this->processWishlistAddProduct($product);
            }

            $this->afterAction();

            $this->setSilenceClose();
        } else {
            parent::doActionAddToWishlist();
        }
    }
}