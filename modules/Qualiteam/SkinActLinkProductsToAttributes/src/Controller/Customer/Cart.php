<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XLite\Model\AttributeValue\AttributeValueCheckbox;
use XLite\Model\AttributeValue\AttributeValueSelect;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{

    /**
     * Add order item to cart
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function addItem($item)
    {
        $result = parent::addItem($item);

        if ($result && !$item->getParentItem()) {

            foreach ($item->getAttributeValues() as $orderItemAttributeValue) {

                $attributeValue = $orderItemAttributeValue->getAttributeValue();

                if ((
                        $attributeValue instanceof AttributeValueCheckbox
                        || $attributeValue instanceof AttributeValueSelect
                    )
                    && $attributeValue->getLinkedProduct()
                    && $attributeValue->getLinkedProduct()->isPublicAvailable()
                ) {

                    $attributeId = $attributeValue->getAttribute()->getId();
                    $amount = Request::getInstance()->linked_product_amount && Request::getInstance()->linked_product_amount[$attributeId] > 0
                        ? Request::getInstance()->linked_product_amount[$attributeId]
                        : 1;

                    $linkedItem = $this->prepareOrderItem(
                        $attributeValue->getLinkedProduct(),
                        $amount
                    );

                    if ($linkedItem) {
                        $this->getCart()->addLinkedItem($linkedItem, $item, $orderItemAttributeValue);
                    }
                }
            }
        }

        return $result;
    }

}