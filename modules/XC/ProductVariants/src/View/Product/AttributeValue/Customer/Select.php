<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\AttributeValue\Customer;

use XC\ProductVariants\Model\ProductVariant;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Attribute value (Select)
 * @Extender\Mixin
 */
abstract class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{
    /**
     * @return mixed|null
     */
    protected function defineAttributeValue()
    {
        /** @var \XLite\Model\AttributeValue\AttributeValueSelect[] $attributeValue */
        $attributeValue = parent::defineAttributeValue();
        $product = $this->getProduct();
        $result = [];

        if ($product->mustHaveVariants()) {
            $selectedIds = $this->getSelectedIds();
            foreach ($attributeValue as $value) {
                $variantAttributeIds = array_replace(
                    $selectedIds,
                    [$value->getAttribute()->getId() => $value->getId()]
                );

                $firstSelectedAttributeId = array_key_first($selectedIds);

                $isSelectedAlready = isset($selectedIds[$value->getAttribute()->getId()])
                    && $selectedIds[$value->getAttribute()->getId()] === $value->getId()
                    && $this->getAttributeDisplayMode() === \XLite\Model\Attribute::SELECT_BOX_MODE;

                $variant = $product->getVariantByAnyAttributeValuesIds($variantAttributeIds);

                if ($variant) {
                    $value->setAvailableAmount($variant->getAvailableAmount());
                }

                if (!$isSelectedAlready) {
                    $variants = Database::getRepo(ProductVariant::class)
                        ->getVariantsByAttributeValue($product->getProductId(), $value->getId());
                    $variantsWithQuantity = array_filter(
                        $variants,
                        static function (ProductVariant $productVariant): bool {
                            return !$productVariant->isOutOfStock();
                        }
                    );

                    $canBeUnavailable = count($selectedIds) === 1 || (count($selectedIds) > 1
                            && $value->getAttribute()->getId() !== $firstSelectedAttributeId);

                    if (
                        $canBeUnavailable
                        && (
                            !$variant
                            || (
                                $this->getAttributeDisplayMode() === \XLite\Model\Attribute::BLOCKS_MODE
                                && (
                                    count($variantsWithQuantity) === 0
                                    || ($variant && $variant->isOutOfStock())
                                )
                            )
                        )
                    ) {
                        $value->setVariantAvailable(false);
                    }

                    if ($variant && $variant->isOutOfStock()) {
                        $value->setVariantOutOfStock(true);
                    }

                    if ($variant && $variant->isShowStockWarning()) {
                        $value->setVariantStockWarning(true);
                    }
                } elseif (
                    !$variant
                    || ($variant && $variant->isOutOfStock())
                ) {
                    foreach ($product->getVariants() as $productVariant) {
                        if (!$productVariant->isOutOfStock()) {
                            foreach ($productVariant->getValues() as $attrValue) {
                                if ($selectedIds[$firstSelectedAttributeId] === $attrValue->getId()) {
                                    $product->setAttrValues($productVariant->getValues());
                                    break 2;
                                }
                            }
                        }
                    }
                }

                $result[] = $value;
            }
        } else {
            $result = $attributeValue;
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getOptionTemplate()
    {
        if ($this->getProduct()->mustHaveVariants()) {
            return 'modules/XC/ProductVariants/product/attribute_value/select/option.twig';
        }

        return parent::getOptionTemplate();
    }
}
