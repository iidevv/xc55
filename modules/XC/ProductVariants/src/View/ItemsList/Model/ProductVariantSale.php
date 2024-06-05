<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Product variants items list
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
class ProductVariantSale extends \XC\ProductVariants\View\ItemsList\Model\ProductVariant
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if ($this->getProduct()->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE) {
            $defaultSale = $this->formatPrice($this->getProduct()->getSalePriceValue());
        } else {
            $defaultSale = $this->getProduct()->getSalePriceValue() . '%';
        }

        if (!$this->getProduct()->getParticipateSale()) {
            $defaultSale = '0%';
        }

        $columns['sale'] = [
            static::COLUMN_NAME      => static::t('Sale'),
            static::COLUMN_SUBHEADER => static::t('Default') . ': ' . $defaultSale,
            static::COLUMN_CLASS     => 'XC\ProductVariants\View\FormField\Inline\Input\Sale',
            static::COLUMN_EDIT_ONLY => true,
            static::COLUMN_ORDERBY   => 450,
        ];

        return $columns;
    }

    /**
     * Pre-validate entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateEntity($entity);

        return $result && $this->prevalidateSaleDiscount($entity);
    }

    /**
     * Pre-validate entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateSaleDiscount(\XLite\Model\AEntity $entity)
    {
        $result = true;

        if ($entity->getDiscountType() == \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT) {
            if (100 < $entity->getSalePriceValue()) {
                $this->errorMessages[] = static::t('Percent discount value cannot exceed 100%');
                $result = false;
            }
        }

        return $result;
    }
}
