<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\AttributeValue\AttributeValueCheckbox;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\OrderItem;

/**
 * Decorate Order model
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{

    /**
     * Increase / decrease item product inventory
     *
     * @param \XLite\Model\OrderItem $item      Order item
     * @param integer                $sign      Flag; "1" or "-1"
     * @param boolean                $register  Register in order history OPTIONAL
     *
     * @return integer
     */
    protected function changeItemInventory($item, $sign, $register = true)
    {
        //Do not change stock for linked products
        if ($item->isAttributeValueLinked()) {
            return  0;
        }

        return parent::changeItemInventory($item, $sign, $register);
    }
    /**
     * Normalize items
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function normalizeItems()
    {
        parent::normalizeItems();

        if (!\XLite::isAdminZone()) {
            $this->updateLinkedProducts();
        }
    }

    public function updateLinkedProducts()
    {
        foreach ($this->getItems() as $item) {

            if (!$item->getParentItem() && $item->getAttributeValues())
            {
                $linkedProductIds = [];
                if ($item->getLinkedItems()) {
                    foreach ($item->getLinkedItems() as $linkedItem) {
                        $linkedProductIds[$linkedItem->getProductId()] = $linkedItem;
                    }
                }

                foreach ($item->getAttributeValues() as $orderItemAttributeValue) {
                    $attributeValue = $orderItemAttributeValue->getAttributeValue();

                    if ((
                            $attributeValue instanceof AttributeValueCheckbox
                            || $attributeValue instanceof AttributeValueSelect
                        )
                        && $attributeValue->getLinkedProduct()
                    ) {
                        if (isset($linkedProductIds[$attributeValue->getLinkedProduct()->getProductId()])) {
                            unset($linkedProductIds[$attributeValue->getLinkedProduct()->getProductId()]);
                        } else {
                            if ($attributeValue->getLinkedProduct()->isPublicAvailable()) {
                                $newLinkedItem = $this->createLinkedOrderItem($attributeValue->getLinkedProduct(), 1);

                                if ($newLinkedItem) {
                                    $this->addLinkedItem($newLinkedItem, $item, $orderItemAttributeValue);
                                }
                            }
                        }
                    }
                }

                //remove invalid linked products
                foreach ($linkedProductIds as $linkedItem) {
                    $this->getItems()->removeElement($linkedItem);
                    if (Database::getEM()->contains($linkedItem)) {
                        Database::getEM()->remove($linkedItem);
                    }
                }
            }
        }
    }

    public function addLinkedItem(OrderItem $linkedItem, OrderItem $item, $attributeValue)
    {
        Database::getEM()->persist($attributeValue);
        Database::getEM()->persist($item);
        Database::getEM()->persist($linkedItem);

        $linkedItem->setParentItem($item);
        $linkedItem->setLinkedAttributeValue($attributeValue);

        $attributeValue->setLinkedOrderItem($linkedItem);
        $item->addLinkedItems($linkedItem);

        $this->addItem($linkedItem);

        Database::getEM()->flush();
    }

    protected function createLinkedOrderItem(\XLite\Model\Product $product = null, $amount = null)
    {
        $item = null;

        if ($product && $product->isPublicAvailable()) {

            $item = new \XLite\Model\OrderItem();
            $item->setOrder($this);

            $item->setAttributeValues(
                $product->prepareDefaultAttributeValues()
            );
            $item->setProduct($product);

            $item->setAmount($amount);

        }

        return $item;
    }
}