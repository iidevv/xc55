<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\Controller\Customer;

use Qualiteam\SkinActPinterestPixel\Helpers\OrderItemTrait;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    use OrderItemTrait;

    /**
     * Print current cart data
     */
    protected function doActionPixelRetrieveOrderData()
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);

        $order = $this->getOrder();
        $result = [];
        $quantity = 0;

        if ($order) {
            $result['order_id'] = $order->getOrderNumber();
            $result['value'] = $order->getTotal();

            if ($currency = $order->getCurrency() ?: \XLite::getInstance()->getCurrency()) {
                $result['currency'] = $currency->getCode();
            } else {
                $result['currency'] = \XLite\View\Model\Currency\Currency::DEFAULT_CURRENCY;
            }

            foreach ($order->getItems() as $key => $oitem) {
                $product = $oitem->getProduct();
                $quantity += $oitem->getAmount();

                $result['line_items'][$key] = [
                    'product_name' => $product->getName(),
                    'product_id' => $this->getUniqueProductId($oitem),
                    'product_price' => $oitem->getDisplayPrice(),
                    'product_quantity' => $oitem->getAmount(),
                    'product_category' => $product->getCategory()->getName(),
                ];

                if ($product->hasVariants()) {
                    $result['line_items'][$key]['product_variant_id'] = $this->getProductVariantId($oitem);
                    $result['line_items'][$key]['product_variant'] = $this->getProductVariantName($oitem);
                }
            }

            $result['order_quantity'] = $quantity;
        }

        $this->displayJSON($result);
    }
}