<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * Cart quantity box
 * @Extender\Mixin
 */
class CartQuantityBox extends \XLite\View\Product\CartQuantityBox
{

    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        $max_amount = parent::getMaxQuantity();

        $orderItem = $this->getOrderItem();

        if ($orderItem && $orderItem->getParentItem()) {
            $configMaxLimit = Config::getInstance()->Qualiteam->SkinActLinkProductsToAttributes->linkedProductMaxQty;
            if ($configMaxLimit > 0) {
                $max_amount = min($orderItem->getParentItem()->getAmount() * $configMaxLimit, $max_amount);
            }
        }

        if ($max_amount > $this->getProduct()->getItemsInCart()) {
            $max_amount = $max_amount - $this->getProduct()->getItemsInCart() + $orderItem->getAmount();
        }

        return $max_amount;
    }
}