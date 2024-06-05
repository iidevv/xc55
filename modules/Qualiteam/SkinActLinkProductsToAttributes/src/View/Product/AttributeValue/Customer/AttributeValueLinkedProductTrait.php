<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\Product\AttributeValue\Customer;

trait AttributeValueLinkedProductTrait
{
    public function hasLinkedProducts()
    {
        foreach ($this->getAttributeValue() as $attrVal) {
            if ($attrVal->getLinkedProduct()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return value is selected or not flag
     *
     * @return \XLite\Model\Product|null
     */
    protected function getLinkedProduct()
    {
        $selectedAttributeValue = $this->getSelectedAttributeValue();

        return $selectedAttributeValue ? $selectedAttributeValue->getLinkedProduct() : null;
    }

    protected function getLinkedProductImage()
    {
        $linkedProduct = $this->getLinkedProduct();

        return $linkedProduct ? $linkedProduct->getImage() : $this->getDefaultLinkedProductImage();
    }

    protected function getDefaultLinkedProductImage()
    {
        foreach ($this->getAvailableLinkedProducts() as $linkedProduct) {
            return $linkedProduct->getImage();
        }

        return null;
    }

    public function getLinkedProductQty()
    {
        $id = $this->getAttribute()->getId();

        return $this->getProduct()->getLinkedProductQty($id);
    }

    public function hasAvailableLinkedProducts()
    {
        return count($this->getAvailableLinkedProducts()) > 0;
    }

    public function getAvailableLinkedProducts()
    {

        $return = [];

        foreach ($this->getAttributeValue() as $attrVal) {
            if ($attrVal->getLinkedProduct() && $attrVal->getLinkedProduct()->isPublicAvailable()) {
                $return[] = $attrVal->getLinkedProduct();
            }
        }

        return $return;
    }
}