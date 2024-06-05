<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\DataMappers;

use XLite;
use XLite\Controller\Customer\Base\Catalog;
use XLite\Core\Cache\ExecuteCached;
use XLite\Model\Category;
use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\IProduct;

class Product extends Common implements IProduct
{
    /**
     * Get impression GA event data
     *
     * Example:
     * [
     *   'id': 'P12345',                   // Product ID (string).
     *   'name': 'Android Warhol T-Shirt', // Product name (string).
     *   'category': 'Apparel',            // Product category (string).
     *   'brand': 'Google',                // Product brand (string).
     *   'variant': 'black',               // Product variant (string).
     *   'price': '29.20',                 // Product price (currency).
     *   'quantity': 1,                    // Product quantity (number)
     *   'coupon': 'APPARELSALE',          // Product coupon (string).
     *   'quantity': 1                     // Product quantity (number).
     * ]
     */
    public function getAddProductData(XLite\Model\Product $product, string $listName = '', string $positionInList = '', string $coupon = '', $qty = null): array
    {
        $data = [
            'price'    => $product->getNetPrice(),
            'coupon'   => $coupon,
            'quantity' => $qty,
        ];

        return array_merge(
            $this->getData($product, $listName, $positionInList),
            $data
        );
    }

    /**
     * Get impression GA event data
     *
     * Example:
     * [
     *   'id'         => 'P12345',                            // Product ID (string).
     *   'name'       => 'Android Warhol T-Shirt',            // Product name (string).
     *   'category'   => 'Apparel/T-Shirts',                  // Product category (string).
     *   'brand'      => 'Google',                            // Product brand (string).
     *   'variant'    => 'Black',                             // Product variant (string).
     *   'list'       => 'Related Products',                  // Product list (string).
     *   'position'   => 1,                                   // Product position (number).
     *   ]
     */
    public function getData(XLite\Model\Product $product, string $listName = '', string $positionInList = ''): array
    {
        $brand    = static::getBrand($product);
        $variant  = static::getVariant($product);
        $category = static::detectCategory($product);

        $data = [
            'id'       => $product->getSku(),
            'name'     => $product->getName(),
            'category' => static::getCategoryName($category),
            'brand'    => $brand,
            'variant'  => $variant,
            'position' => $positionInList,
        ];

        if ($listName) {
            $data['list'] = $listName;
        }

        return $data;
    }

    /**
     * Get product's brand
     *
     * @param XLite\Model\Product $product
     *
     * @return string
     */
    protected static function getBrand(XLite\Model\Product $product): string
    {
        return '';
    }

    /**
     * Get product's variant
     *
     * @param XLite\Model\Product $product
     *
     * @return string
     */
    protected static function getVariant(XLite\Model\Product $product): string
    {
        return '';
    }

    protected static function detectCategory(?XLite\Model\Product $product): ?Category
    {
        $ctrl = XLite::getController();

        if (($ctrl instanceof Catalog) && $ctrl->getCategoryId()) {
            return $ctrl->getCategory();
        }

        if ($product) {
            return $product->getCategory();
        }

        return null;
    }

    public static function getCategoryName(?Category $category): string
    {
        if (!$category) {
            return '';
        }

        return ExecuteCached::executeCachedRuntime(static function () use ($category) {
            $categoryPath = $category->getPath();

            if (count($categoryPath) > 5) {
                array_splice($categoryPath, 4, count($categoryPath) - 5);
            }

            return implode(
                '/',
                array_map(
                    static function ($elem) {
                        return $elem->getName();
                    },
                    $categoryPath
                )
            );
        }, [__CLASS__, __METHOD__, $category->getCategoryId()]);
    }
}
