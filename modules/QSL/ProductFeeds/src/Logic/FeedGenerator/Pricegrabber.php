<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;

/**
 * Class generating feed files for Pricegrabber comparison shopping website.
 *
 * See:
 * - https://partner.pricegrabber.com/mss_main.php?sec=2&ccode=us
 * - https://partner.pricegrabber.com/mss_main.php?sec=1&ccode=us
 */
class Pricegrabber extends ABase
{
    /**
     * Get the feed filename.
     *
     * @return string
     */
    public function getFeedFilename()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->pricegrabber_feed_name;
    }

    /**
     * Get the minimum number of hours that should pass between automatic feed updates.
     *
     * @return integer
     */
    public function getAutoRefreshDelay()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->pricegrabber_refresh_rate;
    }

    /**
     * Define information on CSV columns (key - machine name, value - header)
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku' => [
                'header'   => 'Retsku',
                'required' => true,
                'mapped'   => 'pricegrabber_sku_field',
            ],
            'familySku' => [
                'header'   => 'Parentsku',
                'mapped'   => 'pricegrabber_parentsku_field',
            ],
            'name' => [
                'header' => 'Product Title',
                'required' => true,
            ],
            'description' => [
                'header'   => 'Detailed Description',
            ],
            'category' => [
                'header'   => 'Categorization',
                'required' => true,
            ],
            'productUrl' => [
                'header'   => 'Product URL',
                'required' => true,
            ],
            'imageUrl' => [
                'header'   => 'Primary Image URL',
            ],
            'altImageUrl#1' => [
                'header'   => 'Additional Image URL',
                'name'     => 'altImageUrl',
                'number'   => 1,
            ],
            'altImageUrl#2' => [
                'header'   => 'Additional Image URL',
                'name'     => 'altImageUrl',
                'number'   => 2,
            ],
            'altImageUrl#3' => [
                'header'   => 'Additional Image URL',
                'name'     => 'altImageUrl',
                'number'   => 3,
            ],
            'altImageUrl#4' => [
                'header'   => 'Additional Image URL',
                'name'     => 'altImageUrl',
                'number'   => 4,
            ],
            'altImageUrl#5' => [
                'header'   => 'Additional Image URL',
                'name'     => 'altImageUrl',
                'number'   => 5,
            ],
            'altImageUrl#6' => [
                'header'   => 'Additional Image URL',
                'name'     => 'altImageUrl',
                'number'   => 6,
            ],
            'altImageUrl#7' => [
                'header'   => 'Additional Image URL',
                'name'     => 'altImageUrl',
                'number'   => 7,
            ],
            'altImageUrl#8' => [
                'header'   => 'Additional Image URL',
                'name'     => 'altImageUrl',
                'number'   => 8,
            ],
            'displayPrice' => [
                'header'   => 'Selling Price',
                'required' => true,
            ],
            'listPrice' => [
                'header'   => 'Regular Price',
            ],
            'condition' => [
                'header'   => 'Condition',
                'value'    => 'New',
            ],
            'manufacturer' => [
                'header'   => 'Manufacturer Name',
                'mapped'   => 'pricegrabber_manufacturer_field',
            ],
            'manufacturerPartN' => [
                'header'   => 'Manufacturer Part Number',
                'mapped'   => 'pricegrabber_manufact_num_field',
            ],
            'upc' => [
                'header'   => 'UPC / EAN',
                'mapped'   => 'pricegrabber_upc_field',
            ],
            'isbn' => [
                'header'   => 'ISBN',
                'mapped'   => 'pricegrabber_isbn_field',
            ],
            'inStock' => [
                'header'   => 'Availability',
            ],
            'videoURL' => [
                'header'   => 'Video URL',
                'value'    => '',
            ],
            'color' => [
                'header'   => 'Color',
                'mapped'   => 'pricegrabber_color_field',
            ],
            'size' => [
                'header'   => 'Size',
                'mapped'   => 'pricegrabber_size_field',
            ],
            'age' => [
                'header'   => 'Age',
                'mapped'   => 'pricegrabber_age_field',
            ],
            'gender' => [
                'header'   => 'Gender',
                'mapped'   => 'pricegrabber_gender_field',
            ],
            'shipping' => [
                'header'   => 'Shipping Cost',
            ],
            'weight' => [
                'header'   => 'Weight',
            ],
        ];
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

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->pricegrabber_skip_out_of_stock) {
            $qb->andWhere('(p.amount > :zero) OR (p.inventoryEnabled = 0)')
                ->setParameter('zero', 0);
        }

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->pricegrabber_skip_no_category) {
            $qb->linkInner('p.pricegrabberCategory');
        }

        return $qb;
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
        return substr(parent::getDescriptionColumnValue($column, $item), 0, 1498);
    }

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
        $category = $item->getPricegrabberCategory();

        return $category ? $category->getName() : 'Other > Unclassified > Unclassified';
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
            \XLite\Core\Config::getInstance()->QSL->ProductFeeds->pricegrabber_shipping_field,
            $item
        );
    }

    /**
     * Get value for the weight column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getWeightColumnValue(array $column, FeedItem $item)
    {
        return $item->getWeight($this->getWeightUnits());
    }

    /**
     * Get weight units to be used in the feed.
     *
     * @return string
     */
    protected function getWeightUnits()
    {
        return 'lbs';
    }

    /**
     * Get suffix for URLs included into the feed.
     *
     * @return string
     */
    protected function getUrlSuffix()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->pricegrabber_url_suffix;
    }

    /**
     * Check whether variant attributes should be included into the product name.
     *
     * @return boolean
     */
    protected function isProductNameWithAttributes()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->pricegrabber_variant_names;
    }
}
