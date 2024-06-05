<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;

/**
 * Class generating feed files for NexTag comparison shopping website.
 *
 * See:
 * - http://merchants.nextag.com/serv/main/buyer/SetupImportXmlColumnInfo.jsp
 * - http://www.datafeedwatch.com/help/nextag-fields-descriptions/
 */
class NexTag extends ABase
{
    /**
     * Get the feed filename.
     *
     * @return string
     */
    public function getFeedFilename()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->nextag_feed_name;
    }

    /**
     * Get the minimum number of hours that should pass between automatic feed updates.
     *
     * @return integer
     */
    public function getAutoRefreshDelay()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->nextag_refresh_rate;
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
                'header'   => 'Category: Nextag Numeric ID',
            ],
            'category' => [
                'header'   => 'Category: Other Format',
            ],
            'productUrl' => [
                'header'   => 'Click-Out URL',
                'required' => true,
            ],
            'condition' => [
                'header'   => 'Condition',
                'required' => true,
                'value'    => 'New',
            ],
            'cpc' => [
                'header'   => 'Cost-per-Click',
                'value'    => '',
            ],
            'description' => [
                'header'   => 'Description',
                'required' => true,
            ],
            'distributorId' => [
                'header'   => 'Distributor ID',
                'required' => true,
                'mapped'   => 'nextag_distributor_id_field',
            ],
            'groundShipping' => [
                'header'   => 'Ground Shipping',
                'required' => true,
            ],
            'imageUrl' => [
                'header'   => 'Image URL',
                'required' => true,
            ],
            'ingramParnN' => [
                'header'   => 'Ingram Part #',
                'mapped'   => 'nextag_ingram_id_field',
            ],
            'isbn' => [
                'header'   => 'ISBN',
                'mapped'   => 'nextag_isbn_field',
            ],
            'manufacturer' => [
                'header'   => 'Manufacturer',
                'mapped'   => 'nextag_manufacturer_field',
            ],
            'manufacturerParnN' => [
                'header'   => 'Manufacturer Part #',
                'mapped'   => 'nextag_manufacturer_id_field',
            ],
            'marketingMessage' => [
                'header'   => 'Marketing Message',
                'value'    => '',
            ],
            'muzeId' => [
                'header'   => 'MUZE ID',
                'mapped'   => 'nextag_muze_id_field',
            ],
            'displayPrice' => [
                'header'   => 'Price',
                'required' => true,
            ],
            'name' => [
                'header'   => 'Product Name',
                'required' => true,
            ],
            'sellerPartN' => [
                'header'   => 'Seller Part #',
                'required' => true,
                'mapped'   => 'nextag_seller_id_field',
            ],
            'inStock' => [
                'header'   => 'Stock Status',
                'required' => true,
            ],
            'upc' => [
                'header'   => 'UPC',
                'required' => true,
                'mapped'   => 'nextag_upc_field',
            ],
            'poundWeight' => [
                'header'   => 'Weight',
                'required' => true,
            ],
            'listPrice' => [
                'header'   => 'ListPrice',
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

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->nextag_skip_disabled) {
            $qb->andWhere('p.enabled = :enabled')
                ->setParameter('enabled', 1);
        }

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->nextag_skip_out_of_stock) {
            $qb->andWhere('(p.amount > :zero) OR (p.inventoryEnabled = 0)')
                ->setParameter('zero', 0);
        }

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->nextag_skip_no_category) {
            $qb->linkInner('p.nextagCategory');
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
        $category = $item->getNextagCategory();

        return $category
            ? ($category->getId() . ': ' . $category->getName())
            : '2700020: More Categories / Others';
    }

    /**
     * Get value for the groundShipping column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getGroundShippingColumnValue(array $column, FeedItem $item)
    {
        return $item->getFreeShipping ? 0 : $this->mapProductField(
            \XLite\Core\Config::getInstance()->QSL->ProductFeeds->nextag_ground_shipping_field,
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
    protected function getPoundWeightColumnValue(array $column, FeedItem $item)
    {
        return parent::getPoundWeightColumnValue($column, $item) . ' lb';
    }

    /**
     * Get suffix for URLs included into the feed.
     *
     * @return string
     */
    protected function getUrlSuffix()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->nextag_url_suffix;
    }

    /**
     * Check whether variant attributes should be included into the product name.
     *
     * @return boolean
     */
    protected function isProductNameWithAttributes()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->nextag_variant_names;
    }
}
