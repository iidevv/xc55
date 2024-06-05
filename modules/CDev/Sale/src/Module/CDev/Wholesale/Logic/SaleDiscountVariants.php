<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\Logic;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Net price modificator: price with sale discount
 *
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\Wholesale","XC\ProductVariants"})
 */
class SaleDiscountVariants extends \CDev\Sale\Logic\SaleDiscount
{
    protected static $wholesaleProductVariants = [];

    protected static function isSaleDiscountApplicable(\CDev\Sale\Model\SaleDiscount $saleDiscount, $model)
    {
        $result = parent::isSaleDiscountApplicable($saleDiscount, $model);

        $object = static::getObject($model);
        if (
            $result
            && $object instanceof \XC\ProductVariants\Model\ProductVariant
        ) {
            $controller = \XLite::getController();
            $profile = null;

            $cart = null;
            if ($controller instanceof \XLite\Controller\Customer\ACustomer) {
                $cart = $controller->getCart(true);
                $profile = $cart->getProfile()
                    ?: \XLite\Core\Auth::getInstance()->getProfile();
            }

            if (!$profile) {
                $profile = new \XLite\Model\Profile();
            }

            $key = $object->getUniqueIdentifier() . $profile->getMembershipId();
            if (!isset(static::$wholesaleProductVariants[$key])) {
                static::$wholesaleProductVariants[$key] = false;

                $wholesalePrices = Database::getRepo('CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice')->getWholesalePrices(
                    $object,
                    $profile->getMembership()
                );

                if (empty($wholesalePrices)) {
                    $wholesalePrices = Database::getRepo('CDev\Wholesale\Model\WholesalePrice')->getWholesalePrices(
                        $object->getProduct(),
                        $profile->getMembership()
                    );
                }

                if ($wholesalePrices) {
                    $objectQty = (int) $object->getProduct()->getWholesaleQuantity();

                    if ($objectQty === 0 && $cart) {
                        /** @var $cartItems \XLite\Model\OrderItem[] */
                        $cartItems = $cart->getItemsByProductId($object->getProduct()->getid());

                        foreach ($cartItems as $cartItem) {
                            $objectQty += $cartItem->getAmount();
                        }
                    }

                    $quantityRangeBeginList = array_map(
                        static fn ($wholesalePrice) => $wholesalePrice->getQuantityRangeBegin(),
                        $wholesalePrices
                    );

                    if ($objectQty >= min($quantityRangeBeginList)) {
                        static::$wholesaleProductVariants[$key] = true;
                    }
                }
            }

            $result = !static::$wholesaleProductVariants[$key] || $saleDiscount->getApplyToWholesale();
        }

        return $result;
    }
}
