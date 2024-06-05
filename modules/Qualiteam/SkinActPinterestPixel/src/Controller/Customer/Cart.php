<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\Controller\Customer;

use Qualiteam\SkinActPinterestPixel\Helpers\OrderItemTrait;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    use OrderItemTrait;

    protected function assembleProductAddedToCartEvent($item)
    {
        $eventData = parent::assembleProductAddedToCartEvent($item);

        $currency = \XLite::getInstance()->getCurrency();
        $eventData['pinterestPixelProductData'] = [
            'currency' => $currency->getCode(),
        ];

        $oitems = $this->getCart()->getItems();
        $value = 0.0;

        foreach ($oitems as $key => $oitem) {
            $product = $oitem->getProduct();
            $value += (float) $oitem->getDisplayPrice() * $oitem->getAmount();

            $eventData['pinterestPixelProductData']['line_items'] = [
                $key => [
                    'product_name' => $product->getName(),
                    'product_id' => $this->getUniqueProductId($oitem),
                    'product_price' => $oitem->getDisplayPrice(),
                    'product_quantity' => $oitem->getAmount(),
                    'product_category' => $product->getCategory()->getName(),
                ]
            ];

            if ($product->hasVariants()) {
                $eventData['pinterestPixelProductData']['line_items'][$key]['product_variant_id'] = $this->getProductVariantId($oitem);
                $eventData['pinterestPixelProductData']['line_items'][$key]['product_variant'] = $this->getProductVariantName($oitem);
            }
        }

        $eventData['pinterestPixelProductData']['value'] = $value;
        $eventData['pinterestPixelProductData']['order_quantity'] = $this->getCart()->countQuantity();

        return $eventData;
    }

    protected function processAddItemSuccess($item)
    {
        parent::processAddItemSuccess($item);
        \XLite\Core\Event::pinterestPixelAddedToCart($this->assembleProductAddedToCartEvent($item));
    }
}