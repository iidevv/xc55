<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAmountForFreeShipping\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate OrderItem model
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    protected $calculatedFreeShippingCategories = null;

    public function getCalculatedFreeShippingCategories(int $categoryId)
    {
        if ($this->calculatedFreeShippingCategories === null) {
            $this->calculateFreeShippingCategories();
        }

        return $this->calculatedFreeShippingCategories[$categoryId] ?? null;
    }

    public function calculateFreeShippingCategories(): void
    {
        $this->calculatedFreeShippingCategories = [];

        foreach ($this->getItems() as $item) {
            $product = $item->getProduct();
            $categoryId = $product->getCategoryId();

            if (is_int($categoryId) && !isset($this->calculatedFreeShippingCategories[$categoryId])) {
                $this->calculatedFreeShippingCategories[$categoryId] = [
                    'free_shipping_amount'  => $product->getCategory()->getCategoryAmountShipping(),
                    'total'                 => 0,
                ];
            }

            $this->calculatedFreeShippingCategories[$categoryId]['total'] += $item->getAmount() * $item->getItemPrice();
        }
    }

    public function calculate()
    {
        $this->calculateFreeShippingCategories();

        parent::calculate();
    }
}
