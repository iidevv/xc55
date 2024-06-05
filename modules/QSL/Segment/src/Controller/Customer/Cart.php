<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Cart controller
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * @inheritdoc
     */
    protected function addItem($item)
    {
        $result = parent::addItem($item);

        if ($result) {
            \QSL\Segment\Core\Mediator::getInstance()->doAddProductToCart($item);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function doActionDelete()
    {
        $item = $this->getCart()->getItemByItemId(\XLite\Core\Request::getInstance()->cart_id);

        parent::doActionDelete();

        if ($this->valid) {
            \QSL\Segment\Core\Mediator::getInstance()->doRemoveProductFromCart($item);
        }
    }

    /**
     * @inheritdoc
     */
    protected function doActionClear()
    {
        foreach ($this->getCart()->getItems() as $item) {
            \QSL\Segment\Core\Mediator::getInstance()->doRemoveProductFromCart($item);
        }

        parent::doActionClear();
    }

    /**
     * Update cart
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $quantities = [];
        foreach ($this->getCart()->getItems() as $item) {
            $quantities[$item->getItemId()] = $item->getAmount();
        }

        $shippingId = $this->getCart()->getShippingId();

        parent::doActionUpdate();

        foreach ($quantities as $id => $amount) {
            $item = $this->getCart()->getItemByItemId($id);
            if ($item->getAmount() != $amount) {
                \QSL\Segment\Core\Mediator::getInstance()->doUpdateProductQuantity($item, $amount);
            }
        }

        if ($this->getCart()->getShippingId() != $shippingId) {
            \QSL\Segment\Core\Mediator::getInstance()
                ->doChangeShipping($this->getCart()->getShippingId(), $shippingId);
        }
    }
}
