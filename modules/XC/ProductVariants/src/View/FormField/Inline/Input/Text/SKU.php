<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\FormField\Inline\Input\Text;

/**
 * SKU
 */
class SKU extends \XLite\View\FormField\Inline\Input\Text
{
    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        return parent::getFieldParams($field) + ['maxlength' => 32, 'placeholder' => static::t('Default')];
    }

    /**
     * Validate SKU
     *
     * @param array $field Feild info
     *
     * @return array
     */
    protected function validateSku(array $field)
    {
        $result = [true, null];
        try {
            /** @var \XC\ProductVariants\Model\ProductVariant $productVariant */
            $productVariant = $this->getEntity();
            $validator = new \XC\ProductVariants\Core\Validator\VariantSKU(
                $productVariant ? $productVariant->getId() : null,
                $productVariant && $productVariant->getProduct() ? $productVariant->getProduct()->getId() : null
            );
            $validator->validate($field['widget']->getValue());
        } catch (\Exception $e) {
            $result = [
                false,
                $e->getMessage()
            ];
        }

        return $result;
    }

    /**
     * Get value to write to the database when default value is used (to avoid errors when MySQL works in strict mode)
     *
     * @return integer
     */
    protected function getEmptyFieldValue()
    {
        return '';
    }
}
