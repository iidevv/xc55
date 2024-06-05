<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;

/**
 * Class generating feed files for Shopzilla comparison shopping website.
 *
 * See:
 * - https://merchant.shopzilla.com/pp/resources/us/OA_feed.pdf
 * - https://merchant.shopzilla.com/pp/welcome/taxonomy.xpml
 */
class Shopzilla extends ABase
{
    /**
     * Get the feed filename.
     *
     * @return string
     */
    public function getFeedFilename()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->shopzilla_feed_name;
    }

    /**
     * Get the minimum number of hours that should pass between automatic feed updates.
     *
     * @return integer
     */
    public function getAutoRefreshDelay()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->shopzilla_refresh_rate;
    }

    /**
     * Define information on CSV columns (key - machine name, value - header)
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'categoryId' => [
                'header'   => 'Category ID',
                'required' => true,
            ],
            'manufacturer' => [
                'header'   => 'Manufacturer',
                'mapped'   => 'shopzilla_manufacturer_field',
            ],
            'name' => [
                'header'   => 'Title',
                'required' => true,
            ],
            'description' => [
                'header'   => 'Description',
            ],
            'productUrl' => [
                'header'   => 'Product URL',
                'required' => true,
            ],
            'imageUrl' => [
                'header'   => 'Image URL',
            ],
            'sku' => [
                'header'   => 'SKU',
                'required' => true,
                'mapped'   => 'shopzilla_sku_field',
            ],
            'inStock' => [
                'header'   => 'Availability',
            ],
            'condition' => [
                'header'   => 'Condition',
                'value'    => 'New',
            ],
            'poundWeight' => [
                'header' => 'Ship Weight',
            ],
            'shipping' => [
                'header'   => 'Ship Cost',
                'required' => true,
            ],
            'bid' => [
                'header'   => 'Bid',
                'value'    => '',
            ],
            'promoCode' => [
                'header'   => 'Promotional Code',
                'value'    => '',
            ],
            'upc' => [
                'header'   => 'UPC',
                'mapped'   => 'shopzilla_upc_field',
            ],
            'displayPrice' => [
                'header'   => 'Price',
                'required' => true,
            ],
        ];
    }

    /**
     * Get the char to glue values before writing a row to the feed file.
     *
     * @return string
     */
    protected function getColumnDelimiter()
    {
        return "\t";
    }

    /**
     * Get the char to enclosure complex string values;
     *
     * @return string
     */
    protected function getStringEnclosure()
    {
        return '"';
    }

    /**
     * Get the query builder for retrieving all items matching to the feed criteria.
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb Query builder instance.
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function applyFeedSettings(\XLite\Model\QueryBuilder\AQueryBuilder $qb)
    {
        $qb = parent::applyFeedSettings($qb);

        $qb->andWhere('p.enabled = :enabled')->setParameter('enabled', 1);

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->shopzilla_skip_out_of_stock) {
            $qb->andWhere('(p.amount > :zero) OR (p.inventoryEnabled = 0)')
                ->setParameter('zero', 0);
        }

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->shopzilla_skip_no_category) {
            $qb->linkInner('p.shopzillaCategory');
        }

        return $qb;
    }

    /**
     * Get value for the categoryId column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getCategoryIdColumnValue(array $column, FeedItem $item)
    {
        $category = $item->getShopzillaCategory();

        return $category ? $category->getId() : '20000001';
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
        return substr(parent::getNameColumnValue($column, $item), 0, 98);
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
        return substr(parent::getDescriptionColumnValue($column, $item), 0, 998);
    }

    /**
     * Get value for the Ship Cost column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getShippingColumnValue(array $column, FeedItem $item)
    {
        return $item->getFreeShipping ? '0.00' : $this->mapProductField(
            \XLite\Core\Config::getInstance()->QSL->ProductFeeds->shopzilla_shipping_field,
            $item
        );
    }

    /**
     * Get suffix for URLs included into the feed.
     *
     * @return string
     */
    protected function getUrlSuffix()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->shopzilla_url_suffix;
    }

    /**
     * Check whether variant attributes should be included into the product name.
     *
     * @return boolean
     */
    protected function isProductNameWithAttributes()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->shopzilla_variant_names;
    }
}
