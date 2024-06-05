<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Add item to order
     *
     * @param \XLite\Model\OrderItem $newItem Item to add
     *
     * @return boolean
     */
    public function addItem(\XLite\Model\OrderItem $newItem)
    {
        $result = parent::addItem($newItem);

        if ($result && $newItem->isValid()) {
            $minQuantity = \XLite\Core\Database::getRepo('CDev\Wholesale\Model\MinQuantity')
                ->getMinQuantity(
                    $newItem->getProduct(),
                    $this->getProfile() ? $this->getProfile()->getMembership() : null
                );

            if ($minQuantity && $newItem->getAmount() < $minQuantity->getQuantity()) {
                $newItem->setAmount($minQuantity->getQuantity());
            }
        }

        return $result;
    }

    /**
     * Check order subtotal
     *
     * @return boolean
     */
    public function isMaxOrderAmountError()
    {
        $result = parent::isMaxOrderAmountError();
        $items = $this->getItems();

        if ($result && count($items) === 1) {
            $product = $items[0]->getProduct();

            if ($product->getMaxPurchaseLimit() === $product->getMinQuantity($product->getCurrentMembership())) {
                $result = false;
            }
        }

        return $result;
    }
}
