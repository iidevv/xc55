<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Helpers;

use XLite\Model\Cart;

class Order
{
    /**
     * @param \Qualiteam\SkinActKlarna\Helpers\Converter $converter
     * @param \XLite\Model\Cart                          $cart
     */
    public function __construct(
        private Converter $converter,
        private Cart      $cart
    ) {
    }

    public function getOrderLines(): array
    {
        $result = [];

        /** @var \XLite\Model\OrderItem $item */
        foreach ($this->cart->getItems() as $item) {

            // For fields 'tax_rate' and 'total_tax_amount'
            // Since Avatax service will not apply tax on the item level we have "0" value here

            $result[] = [
                'reference'             => $item->getSku(),
                'name'                  => $item->getName(),
                'quantity'              => $item->getAmount(),
                'unit_price'            => $this->converter->getItemPrice($item),
                'tax_rate'              => 0,
                'total_amount'          => $this->converter->getItemTotal($item),
                'total_discount_amount' => $this->converter->getItemDiscountAmount($item),
                'total_tax_amount'      => 0,
            ];
        }

        // Best practices Klarna
        // https://docs.klarna.com/klarna-payments/in-depth-knowledge/tax-handling/#tax-handling-best-practices-transmitting-tax-in-the-us

        if (!empty($result)) {
            $result[] = [
                'type'         => "sales_tax",
                'name'         => "Tax",
                'quantity'     => 1,
                'unit_price'   => $this->converter->getOrderTaxAmount($this->cart),
                'total_amount' => $this->converter->getOrderTaxAmount($this->cart),
            ];

            $result[] = [
                'type'         => "discount",
                'name'         => "Discount",
                'quantity'     => 1,
                'unit_price'   => $this->converter->getOrderDiscountAmount($this->cart),
                'total_amount' => $this->converter->getOrderDiscountAmount($this->cart),
            ];

            $result[] = [
                'type'         => "shipping_fee",
                'name'         => empty($this->cart->getShippingMethodName()) ? "default_shipping" : $this->cart->getShippingMethodName(),
                'quantity'     => 1,
                'unit_price'   => $this->converter->getShippingMethodAmount($this->cart),
                'total_amount' => $this->converter->getShippingMethodAmount($this->cart),
            ];
        }

        return $result;
    }
}