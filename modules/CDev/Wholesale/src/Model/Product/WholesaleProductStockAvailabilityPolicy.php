<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Model\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Controller\Customer\ACustomer;
use XLite\Model\Cart;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 */
class WholesaleProductStockAvailabilityPolicy extends \XLite\Model\Product\ProductStockAvailabilityPolicy
{
    public const PRODUCT_MIN_QUANTITY = 'min_quantity';

    public function isOutOfStock(Cart $cart)
    {
        return parent::isOutOfStock($cart)
               || $this->getAvailableAmount($cart) < $this->dto[self::PRODUCT_MIN_QUANTITY];
    }

    protected function createDTO(Product $product)
    {
        $controller = \XLite::getController();

        $membership = $controller instanceof ACustomer
            ? ($controller->getCart()->getProfile() ? $controller->getCart()->getProfile()->getMembership() : null)
            : null;

        return parent::createDTO($product) + [
            self::PRODUCT_MIN_QUANTITY => $product->getMinQuantity($membership),
        ];
    }
}
