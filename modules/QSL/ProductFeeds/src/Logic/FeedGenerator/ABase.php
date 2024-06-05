<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;
use Includes\Utils\Module\Manager;

/**
 * Class with methods for exporting basic product fields.
 */
abstract class ABase extends AFeedGeneratror
{
    /**
     * Get value for the category column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getCategoryColumnValue(array $column, FeedItem $item)
    {
        return $this->getCategoryPath($item->getCategory(), ' > ');
    }

    /**
     * Get value for the productUrl column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getProductUrlColumnValue(array $column, FeedItem $item)
    {
        if ($productId = $item->getProductId()) {
            $params = ['product_id' => $productId];

            return $this->buildFeedUrl('product', '', $params);
        }

        return null;
    }

    /**
     * Get value for the description column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getDescriptionColumnValue(array $column, FeedItem $item)
    {
        return strtr(
            strip_tags($item->getCommonDescription()),
            [
                "\r" => '',
                "\n" => ' ',
                "\t" => ' ',
            ]
        );
    }

    /**
     * Get value for the imageUrl column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getImageUrlColumnValue(array $column, FeedItem $item)
    {
        return $item->hasImage() ? $item->getImageURL() : '';
    }

    /**
     * Get value for the imageUrl column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getAltImageUrlColumnValue(array $column, FeedItem $item)
    {
        $number = isset($column['number']) ? (int) $column['number'] : 1;

        return ($item->countImages() > $number)
            ? $item->getImages()->get($number)->getURL()
            : '';
    }

    /**
     * Get value for the imageUrls column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getAltImageUrlsColumnValue(array $column, FeedItem $item)
    {
        $urls = [];
        foreach ($item->getImages() as $image) {
            $urls[] = $image->getURL();
        }

        $delimiter = $column['delimiter'] ?? ',';

        return empty($urls) ? '' : implode($delimiter, $urls);
    }

    /**
     * Get value for the name column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getNameColumnValue(array $column, FeedItem $item)
    {
        $name = $item->getName();

        if (
            $this->isProductNameWithAttributes()
            && \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'ProductVariants')
            && ($item->getVariantsAttributes()->count() > 0)
        ) {
            $name .= ' ' . $this->getVariantAttributeValuesString($item);
        }

        return strip_tags($name);
    }

    /**
     * Get values of variant attributes joined into a single string to add to product name.
     *
     * @param \QSL\ProductFeeds\Core\FeedItem $item Feed item.
     *
     * @return string
     */
    protected function getVariantAttributeValuesString(FeedItem $item)
    {
        return '(' . implode(', ', $this->getVariantAttributeValues($item)) . ')';
    }

    /**
     * Get values of variant attributes.
     *
     * @param \QSL\ProductFeeds\Core\FeedItem $item Feed item.
     *
     * @return array
     */
    protected function getVariantAttributeValues(FeedItem $item)
    {
        $values = [];

        foreach ($item->getVariantsAttributes() as $attribute) {
            $values[] = $item->getAttributeValue($attribute);
        }

        return $values;
    }

    /**
     * Check whether variant attributes should be included into the product name.
     *
     * @return boolean
     */
    protected function isProductNameWithAttributes()
    {
        return true;
    }

    /**
     * Get value for the stock column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getInStockColumnValue(array $column, FeedItem $item)
    {
        return $item->isOutOfStock() ? 'Out of Stock' : 'In Stock';
    }

    /**
     * Get value for the weight column in pounds.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getPoundWeightColumnValue(array $column, FeedItem $item)
    {
        return $item->getWeight('lbs');
    }

    /**
     * Get value for the weight column in kg.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getKgWeightColumnValue(array $column, FeedItem $item)
    {
        return $item->getWeight('kg');
    }

    /**
     * Get value for the listPrice column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getListPriceColumnValue(array $column, FeedItem $item)
    {
        $result = '';

        if (
            Manager::getRegistry()->isModuleEnabled('CDev', 'MarketPrice')
            && $item->getDisplayMarketPrice() > $item->getDisplayPrice()
        ) {
            $result = $item->getDisplayMarketPrice();
        }

        return $result;
    }

    /**
     * Get position of the product in the list of top sellers.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getSalesRankColumnValue(array $column, FeedItem $item)
    {
        // TODO: Add Sales Rank
        return '';
    }

    /**
     * Get the launch date for coming soon and preorder products.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getLaunchDateColumnValue(array $column, FeedItem $item)
    {
        return (
            \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('CDev', 'ProductAdvisor')
                && $item->isUpcomingProduct()
            )
            ? $item->getArrivalDate()
            : '';
    }

    /**
     * Get URL for the mobile version of the product page.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getMobileUrlColumnValue(array $column, FeedItem $item)
    {
        return \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'Mobile')
          ? $this->getProductUrlColumnValue($column, $item)
          : '';
    }

    /**
     * Get comma-separated SKUs of related products.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getRelatedProductsColumnValue(array $column, FeedItem $item)
    {
        // TODO: Add Related Products field
        return '';
    }
}
