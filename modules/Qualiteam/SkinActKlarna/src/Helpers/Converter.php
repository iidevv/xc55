<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Helpers;

use XLite\Model\Base\Surcharge;
use XLite\Model\Cart;
use XLite\Model\Profile;
use XLite\Model\OrderItem;
use XLite\Model\Payment\BackendTransaction;

class Converter
{
    public function getShippingMethodAmount(Cart $cart): float
    {
        return $this->getRoundedValue(
            $cart->getSurchargeSumByType(Surcharge::TYPE_SHIPPING)
        );
    }

    public function getOrderTaxAmount(Cart $cart): float
    {
        return $this->getRoundedValue(
            $cart->getSurchargeSumByType(Surcharge::TYPE_TAX)
        );
    }

    public function getOrderDiscountAmount(Cart $cart): float
    {
        return $this->getRoundedValue(
            $cart->getSurchargeSumByType(Surcharge::TYPE_DISCOUNT)
        );
    }

    protected function getRoundedValue(float $value): float
    {
        return round($this->getFormattedValue($value));
    }

    protected function getFormattedValue(float $value): float
    {
        return $value * 100;
    }

    public function getOrderAmount(Cart $cart): float
    {
        return $this->getRoundedValue(
            $cart->getTotal()
        );
    }

    public function getLanguageCode(Profile $profile): string
    {
        return $this->getStrLowerValue(
            $profile->getLanguage()
        );
    }

    protected function getStrLowerValue(string $value): string
    {
        return strtolower($value);
    }

    public function getItemPrice(OrderItem $item): float
    {
        return $this->getRoundedValue(
            $item->getItemNetPrice()
        );
    }

    public function getItemTotal(OrderItem $item): float
    {
        return $this->getRoundedValue(
            $item->getTotal()
        );
    }

    public function getItemDiscountAmount(OrderItem $item): float
    {
        return $this->getRoundedValue(
            $item->getSubtotal() - $item->getTotal()
        );
    }

    public function getCountryCode(Profile $profile): string
    {
        return $this->getStrToUpper(
            $profile->getBillingAddress()->getCountryCode()
        );
    }

    protected function getStrToUpper(string $value): string
    {
        return strtoupper($value);
    }

    public function getRefundedAmount(BackendTransaction $backendTransaction)
    {
        return $this->getRoundedValue(
            $backendTransaction->getValue()
        );
    }
}