<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use XC\ProductVariants\Model\Product as ProductVariantsProduct;
use XC\SystemFields\Model\Product as SystemFieldsProduct;
use XC\SystemFields\Module\XC\ProductVariants\Model\ProductVariant as SystemFieldsProductVariant;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;


/**
 * CloudSearch store-side API methods
 *
 * @Extender\Mixin
 * @Extender\Depend ({"XC\SystemFields"})
 */
class StoreApiSystemFields extends \QSL\CloudSearch\Core\StoreApi
{
    /**
     * Get searchable product attributes data
     *
     * @param $product
     *
     * @return array
     */
    protected function getSearchableProductAttributes(Product $product)
    {
        $codes = $this->getActiveLanguages();

        $attributes = parent::getSearchableProductAttributes($product);

        $upcIsbn   = [];
        $mnfVendor = [];

        foreach ($codes as $code) {
            $upcIsbn["name_$code"] = (string) static::t('UPC/ISBN', [], $code);
            /** @var SystemFieldsProduct $product */
            $upcIsbn["values_$code"] = [$product->getUpcIsbn()];

            $mnfVendor["name_$code"] = (string) static::t('Mnf/Vendor', [], $code);
            /** @var SystemFieldsProduct $product */
            $mnfVendor["values_$code"] = [$product->getMnfVendor()];

            if (method_exists($product,'hasVariants') && $product->hasVariants()) {
                /** @var ProductVariantsProduct $product */
                /** @var SystemFieldsProductVariant $variant */
                foreach ($product->getVariants() as $variant) {
                    $upcIsbn["values_$code"][]   = $variant->getUpcIsbn();
                    $mnfVendor["values_$code"][] = $variant->getMnfVendor();
                }
            }

            $upcIsbn["values_$code"]   = array_filter(array_unique($upcIsbn["values_$code"]));
            $mnfVendor["values_$code"] = array_filter(array_unique($mnfVendor["values_$code"]));

            if (empty($upcIsbn["values_$code"])) {
                unset($upcIsbn["name_$code"], $upcIsbn["values_$code"]);
            }

            if (empty($mnfVendor["values_$code"])) {
                unset($mnfVendor["name_$code"], $mnfVendor["values_$code"]);
            }
        }

        if (!empty($upcIsbn)) {
            $attributes[] = $upcIsbn;
        }

        if (!empty($mnfVendor)) {
            $attributes[] = $mnfVendor;
        }

        return $attributes;
    }
}
