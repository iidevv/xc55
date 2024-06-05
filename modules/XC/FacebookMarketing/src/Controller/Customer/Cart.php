<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Class Cart
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * Returns event data
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    protected function assembleProductAddedToCartEvent($item)
    {
        $eventData = parent::assembleProductAddedToCartEvent($item);

        $valuePercentage = (float) \XLite\Core\Config::getInstance()->XC->FacebookMarketing->add_to_cart_value;

        $currency = \XLite::getInstance()->getCurrency();
        $eventData['fbPixelProductData'] = [
            'content_ids' => $this->getItemId($item),
            'content_type' => 'product',
            'currency' => $currency->getCode(),
            'value' => $currency->roundValue($item->getNetPrice() * $item->getAmount() * ($valuePercentage / 100)),
        ];

        return $eventData;
    }

    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return integer
     */
    protected function getItemId($item)
    {
        return $item->getObject()->getSku();
    }
}
