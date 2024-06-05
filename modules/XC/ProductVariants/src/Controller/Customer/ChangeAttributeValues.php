<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Change attribute values from cart / wishlist item
 * @Extender\Mixin
 */
class ChangeAttributeValues extends \XLite\Controller\Customer\ChangeAttributeValues
{
    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage = null;

    /**
     * Change product attribute values
     *
     * @param array $attributeValues Attrbiute values (prepared, from request)
     *
     * @return boolean
     */
    protected function saveAttributeValues(array $attributeValues)
    {
        $result = true;

        if ($this->getItem()->getProduct()->mustHaveVariants()) {
            $variant = $this->getItem()->getProduct()->getVariantByAttributeValues($attributeValues);
            $currentVariant = $this->getItem()->getVariant();

            if (!$variant || !$currentVariant || $variant->getId() !== $currentVariant->getId()) {
                if ($variant && 0 < $variant->getMaxAmount()) {
                    $this->getItem()->setVariant($variant);
                } else {
                    $result             = false;
                    $this->errorMessage = static::t(
                        'Product with selected attribute value(s) is not available or out of stock. Please select other.'
                    );
                }
            }
        }

        return $result && parent::saveAttributeValues($attributeValues);
    }

    /**
     * Get error message
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return $this->errorMessage ?: parent::getErrorMessage();
    }
}
