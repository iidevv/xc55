<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Event;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Model\OrderItem;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * @return \XLite\Model\OrderItem|null
     */
    protected function getPreparedOrderItem(): ?OrderItem
    {
        if (Request::getInstance()->freqboth) {
            $this->prepareFreqBoughtTogetherRequest();
        }

        return parent::getPreparedOrderItem();
    }

    /**
     * @return void
     */
    protected function prepareFreqBoughtTogetherRequest(): void
    {
        Request::getInstance()->freq_bought_together_mode = true;
    }

    /**
     * @param \XLite\Model\OrderItem|null $item
     *
     * @return void
     */
    protected function tryToAddProduct(OrderItem $item = null): void
    {
        if (Request::getInstance()->freqboth) {
            if ($this->addItem($item)) {
                $this->processAddFreqBoughtTogetherItemSuccess($item);
                $this->getSuccessTopMessageAfterAddToCartFreqBoughtProducts();
            } else {
                $this->processAddItemError();
                $this->isProductAdded['success'] = false;
            }
        } else {
            parent::tryToAddProduct($item);
        }
    }

    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return void
     */
    protected function processAddFreqBoughtTogetherItemSuccess(OrderItem $item): void
    {
        Event::productAddedToCart($this->assembleProductAddedToCartEvent($item));
    }

    /**
     * @return void
     */
    protected function getSuccessTopMessageAfterAddToCartFreqBoughtProducts(): void
    {
        TopMessage::addInfo('SkinActFrequentlyBoughtTogether selected products are added to your cart');
    }

    /**
     * Process 'Add item' error
     *
     * @return void
     */
    protected function processAddItemError(): void
    {
        if ($this->currentItem && !$this->currentItem->isAvailable()) {
            TopMessage::addWarning('SkinActFrequentlyBoughtTogether the product you are trying to add to cart is unavailable', [
                'product' => '<a href="' . $this->buildURL(
                        'product',
                        [],
                        ['product_id' => $this->currentItem->getProductId()]) . '">' . $this->currentItem->getName() . '</a>',
            ]);
        } else {
            parent::processAddItemError();
        }
    }
}