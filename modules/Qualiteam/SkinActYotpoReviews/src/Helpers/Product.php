<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Helpers;

use XLite\Core\Database;
use XLite\Model\Product as ProductModel;

class Product
{
    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return string|null
     */
    public function getSku(?ProductModel $product): ?string
    {
        if ($product) {
            return !empty($product->getSku())
                ? $product->getSku()
                : Database::getRepo(ProductModel::class)->generateSKU($product);
        }

        return '';
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return string|null
     */
    public function getName(?ProductModel $product): ?string
    {
        return $product ? $product->getName() : '';
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return string|null
     */
    public function getDescription(?ProductModel $product): ?string
    {
        return $product ? strip_tags($product->getCommonDescription()) : '';
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return float|null
     */
    public function getPrice(?ProductModel $product): ?float
    {
        return $product ? (float) $product->getDisplayPrice() : 0;
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return string|null
     */
    public function getUrl(?ProductModel $product): ?string
    {
        if ($product) {
            return LC_USE_CLEAN_URLS
                ? \XLite::getInstance()->getShopURL($product->getCleanURL())
                : $product->getFrontURL();
        }

        return '';
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return int|null
     */
    public function getQuantity(?ProductModel $product): ?int
    {
        return $product ? $product->getPublicAmount() : 0;
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return string|null
     */
    public function getBrand(?ProductModel $product): ?string
    {
        return $product ? $product->getBrandName() : '';
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return string|null
     */
    public function getImageUrl(?ProductModel $product): ?string
    {
        return $product && $product->getImageURL() ? $product->getImageURL() : '';
    }
}