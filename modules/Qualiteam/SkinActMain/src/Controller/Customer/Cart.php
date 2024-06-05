<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Controller\Customer;

use Qualiteam\SkinActFreeGifts\Core\AddGiftValidator;
use Qualiteam\SkinActMain\Core\AddProductValidator;
use Qualiteam\SkinActMain\Core\IAddProductValidator;
use Qualiteam\SkinActMain\Exception\NoProductsToAdd;
use Qualiteam\SkinActMain\Exception\NotValidProducts;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Event;
use XLite\Core\Request;
use XLite\Model\OrderItem;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * @var Product|null
     */
    protected $currentItem = null;

    /**
     * @var array|true[]
     */
    protected $isProductAdded = ['success' => true];


    protected function getValidator(): IAddProductValidator
    {
        return new AddProductValidator();
    }

    protected function getRuntimeValidator(): IAddProductValidator
    {
        return $this->executeCachedRuntime(function () {
            return $this->getValidator();
        }, [
            __METHOD__,
            self::class,
        ]);
    }

    protected function markCartCalculate()
    {
        return $this->getAction() === 'add_items'
            || parent::markCartCalculate();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function doActionAddItems()
    {
        try {
            $products = $this->getProductsToAdd();

            $this->validateProductsAgainstCartItems($products);

            foreach ($products as $item) {
                $this->currentItem = $item;
                $preparedItem = $this->getPreparedOrderItem();

                $this->tryToAddProduct($preparedItem);
            }

            $this->currentItem = null;

            $this->updateCart();

        } catch (\Throwable $exception) {

            $this->handleNotValidProducts($exception);
        }

        Event::getInstance()->display();
        Event::getInstance()->clear();

        $this->printAJAX($this->isProductAdded);
        exit(0);
    }

    /**
     * @param Product[] $products
     *
     * @throws NotValidProducts
     */
    protected function validateProductsAgainstCartItems(array $products)
    {
        $validator = $this->getRuntimeValidator();

        foreach ($products as $product) {
            if (!$validator->isValid($product)) {
                throw new NotValidProducts();
            }
        }
    }

    protected function handleNotValidProducts(NotValidProducts $exception)
    {
        if ($exception->getMessage()) {
            \XLite\Core\TopMessage::addWarning($exception->getMessage());
        } else {
            \XLite\Core\TopMessage::addWarning(get_class($exception));
        }

        $this->isProductAdded['success'] = false;
    }

    /**
     * @return array
     * @throws NoProductsToAdd
     */
    protected function getProductsToAdd()
    {
        $items = Request::getInstance()->productIds ?? [Request::getInstance()->product_id];

        if (!$items) {
            throw new NoProductsToAdd();
        }

        return Database::getRepo(Product::class)->findByIds($items);
    }

    /**
     * @return \XLite\Model\OrderItem|null
     */
    protected function getPreparedOrderItem(): ?OrderItem
    {
        $this->prepareProductsAttributeValues();

        return $this->prepareOrderItem(
            $this->currentItem,
            $this->isSetCurrentAmount() ? $this->getCurrentAmount() : null
        );
    }

    /**
     * @return array
     */
    protected function prepareProductsAttributeValues()
    {
        return Request::getInstance()->attribute_values ?? [];
    }

    /**
     * @param \XLite\Model\OrderItem|null $item
     *
     * @return void
     */
    protected function tryToAddProduct(OrderItem $item = null)
    {
        if ($this->addItem($item)) {
            $this->processAddItemSuccess($item);
        } else {
            $this->processAddItemError();
            $this->isProductAdded['success'] = false;
        }
    }
}
