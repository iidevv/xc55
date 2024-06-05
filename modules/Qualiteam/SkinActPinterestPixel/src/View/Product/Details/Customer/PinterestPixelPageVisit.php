<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\View\Product\Details\Customer;

use Includes\Utils\Converter;
use Qualiteam\SkinActPinterestPixel\Main;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request;
use XLite\Model\Product;

/**
 * @ListChild (list="product.details.page.info", weight="5")
 * @ListChild (list="product.details.quicklook.info", weight="5")
 */
class PinterestPixelPageVisit extends \XLite\View\Product\Details\Customer\Widget
{
    public function getFingerprint()
    {
        return 'widget-fingerprint-pinterest-value';
    }

    protected function getDefaultTemplate()
    {
        return Main::getModulePath() . '/page_visit.twig';
    }

    public function getUniqueProductId()
    {
        $variantId = (int) Request::getInstance()->variant_id ?? 0;

        /** @var Product $product */
        $product = $this->getProduct();

        if ($product) {
            if ($variantId === 0 && $product->mustHaveVariants()) {
                $variant = $product->getDefaultVariant() ?? $product->getVariants()->first();
                $variantId = $variant->getId();

                if (Request::getInstance()->attribute_values) {
                    $variantId = $this->getVariantByAttributeValuesInRequest();
                }
            }

            return $variantId > 0 ? $product->getProductId() . "_" . $variantId : $product->getProductId();
        }

        return 0;
    }

    protected function getVariantByAttributeValuesInRequest()
    {
        $attributeValuesArr = $this->prepareAttributeValuesByRequest();
        $values = [];

        foreach($attributeValuesArr as $value) {
            $result = explode('_', $value);
            $values[$result[0]] = $result[1];
        }

        $variant = $this->prepareProductVariantByAttributeValues($values);

        return $variant ? $variant->getId() : null ;
    }

    protected function prepareAttributeValuesByRequest(): array
    {
        $attributeValuesArr = explode(',', Request::getInstance()->attribute_values);

        return array_filter($attributeValuesArr, function($item) {
            return !empty($item);
        });
    }

    protected function prepareProductVariantByAttributeValues(array $values)
    {
        return $this->getProduct()->getVariantByAttributeValues($values);
    }

    protected function getUniqueContentId()
    {
        $product = $this->getProduct();

        $result = $product->getSku();

        if ($product->mustHaveVariants()) {
            $variant = $product->getDefaultVariant() ?? $product->getVariants()->first();
            $result = $variant->getSku() ?: $variant->getVariantId();
        }

        return $result;
    }

    protected function getCurrencyCode()
    {
        return \XLite::getInstance()->getCurrency()->getCode();
    }

    public function getProductPrice()
    {
        $product = $this->getProduct();

        if ($product) {
            $price = $product->hasVariants() && $this->getProductVariant()
                ? $this->getProductVariant()->getDisplayPrice()
                : $product->getNetPrice();

            return $this->getProduct() ? Converter::formatPrice($price) : 0;
        }

        return 0;
    }

    public function isProductHasCategory()
    {
        return $this->getProduct()
            && $this->getProduct()->getCategory();
    }

    public function getProductCategoryName()
    {
        return $this->getProduct()->getCategory()->getName();
    }
}