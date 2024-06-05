<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Helpers;

use Includes\Utils\Converter;
use XC\ProductVariants\Model\ProductVariant as ProductVariantModel;

class ProductVariant
{
    /**
     * @param \XC\ProductVariants\Model\ProductVariant|null $variant
     *
     * @return string
     */
    public function getSku(?ProductVariantModel $variant): string
    {
        if ($variant) {
            return $variant->getProduct()->getSku();
        }

        return '';
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant|null $variant
     *
     * @return string
     */
    public function getImageUrl(?ProductVariantModel $variant): string
    {
        return $variant && $variant->getImage() ? $variant->getImage()->getURL() : '';
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant|null $variant
     *
     * @return float
     */
    public function getPrice(?ProductVariantModel $variant): float
    {
        return $variant ? $variant->getDisplayPrice() : 0;
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant|null $variant
     *
     * @return int
     */
    public function getQuantity(?ProductVariantModel $variant): int
    {
        return $variant ? $variant->getAvailableAmount() : 0;
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant|null $variant
     *
     * @return string
     */
    public function getName(?ProductVariantModel $variant): string
    {
        $attrsString = array_reduce($variant?->getValues(), static function ($str, $attr) {
            $str .= $attr->asString() . ' ';
            return $str;
        }, '');

        return $variant?->getProduct()->getName() . ' ' . trim($attrsString);
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant|null $variant
     *
     * @return string
     */
    public function getUrl(?ProductVariantModel $variant): string
    {
        $values = array_reduce($variant?->getValues(), static function ($obj, $value) {
            $obj[$value->getAttribute()->getId()] = $value->getId();
            return $obj;
        }, []);

        return $variant?->getProduct()->getProductId()
            ? \XLite::getInstance()->getShopURL(
                Converter::buildURL(
                    'product',
                    '',
                    [
                        'product_id'       => $variant?->getProduct()->getProductId(),
                        'attribute_values' => $values,
                    ],
                    \XLite::getCustomerScript()
                )
            )
            : '';
    }
}