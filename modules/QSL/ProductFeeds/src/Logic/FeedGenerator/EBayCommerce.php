<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;

/**
 * Class generating feed files for eBay Commerce Network comparison shopping website.
 *
 * See:
 * - http://merchantsupport.shopping.com/Getting_Started
 * - http://merchantsupport.shopping.com/files/ECN_Datafeed_Spec_2013.xlsx
 */
class EBayCommerce extends ABase
{
    /**
     * Get the feed filename.
     *
     * @return string
     */
    public function getFeedFilename()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->ebaycomnet_feed_name;
    }

    /**
     * Get the minimum number of hours that should pass between automatic feed updates.
     *
     * @return integer
     */
    public function getAutoRefreshDelay()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->ebaycomnet_refresh_rate;
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
                'header'   => 'Unique Merchant SKU',
                'required' => true,
                'mapped'   => 'ebaycomnet_sku_field',
            ],
            'name' => [
                'header' => 'Product Name',
                'required' => true,
            ],
            'productUrl' => [
                'header'   => 'Product URL',
                'required' => true,
            ],
            'imageUrl' => [
                'header'   => 'Primary Image URL',
                'required' => true,
            ],
            'price' => [
                'header'   => 'Current Price',
                'required' => true,
            ],
            'inStock' => [
                'header'   => 'Stock Availability',
                'required' => true,
            ],
            'condition' => [
                'header'   => 'Condition',
                'value'    => 'New',
                'required' => true,
            ],
            'isbn' => [
                'header'   => 'MPN/ISBN',
                'required' => true,
                'mapped'   => 'ebaycomnet_isbn_field',
            ],
            'upc' => [
                'header'   => 'UPC or EAN',
                'required' => true,
                'mapped'   => 'ebaycomnet_upc_field',
            ],
            'shipping' => [
                'header'   => 'Shipping Rate',
            ],
            'listPrice' => [
                'header'   => 'Original Price',
            ],
            'coupon' => [
                'header'   => 'Coupon Code',
                'value'    => '',
            ],
            'couponDescription' => [
                'header'   => 'Coupon Code Description',
                'value'    => '',
            ],
            'manufacturer' => [
                'header'   => 'Brand / Manufacturer',
                'mapped'   => 'ebaycomnet_manufacturer_field',
            ],
            'description' => [
                'header'   => 'Product Description',
            ],
            'type' => [
                'header'   => 'Product Type',
                'mapped'   => 'ebaycomnet_type_field',
            ],
            'category' => [
                'header'   => 'Category',
            ],
            'categoryId' => [
                'header'   => 'Category ID',
            ],
            'familySku' => [
                'header'   => 'Parent SKU',
                'mapped'   => 'ebaycomnet_parentsku_field',
            ],
            'familyName' => [
                'header'   => 'Parent Name',
            ],
            'salesRank' => [
                'header'   => 'Top Seller Rank',
            ],
            'estimatedDate' => [
                'header'   => 'Estimated Ship Date',
                'mapped'   => 'ebaycomnet_estdate_field'
            ],
            'gender' => [
                'header'   => 'Gender',
                'mapped'   => 'ebaycomnet_gender_field'
            ],
            'color' => [
                'header'   => 'Color',
                'mapped'   => 'ebaycomnet_color_field',
            ],
            'material' => [
                'header'   => 'Material',
                'mapped'   => 'ebaycomnet_material_field',
            ],
            'size' => [
                'header'   => 'Size',
                'mapped'   => 'ebaycomnet_size_field',
            ],
            'sizeUnits' => [
                'header'   => 'Size Unit of Measure',
            ],
            'age' => [
                'header'   => 'Age Range',
                'mapped'   => 'ebaycomnet_age_field',
            ],
            'cellPlan' => [
                'header'   => 'Cell Phone Plan Type',
                'mapped'   => 'ebaycomnet_plan_field',
            ],
            'cellProvider' => [
                'header'   => 'Cell Phone Service Provider',
                'mapped'   => 'ebaycomnet_provider_field',
            ],
            'stockDescription' => [
                'header'   => 'Stock Description',
                'value'    => '',
            ],
            'launchDate' => [
                'header'   => 'Product Launch Date',
            ],
            'feature1' => [
                'header'   => 'Product Bullet Point 1',
                'value'    => '',
            ],
            'feature2' => [
                'header'   => 'Product Bullet Point 2',
                'value'    => '',
            ],
            'feature3' => [
                'header'   => 'Product Bullet Point 3',
                'value'    => '',
            ],
            'feature4' => [
                'header'   => 'Product Bullet Point 4',
                'value'    => '',
            ],
            'feature5' => [
                'header'   => 'Product Bullet Point 5',
                'value'    => '',
            ],
            'altImageUrl#1' => [
                'header'   => 'Alternative Image URL 1',
                'name'     => 'altImageUrl',
                'number'   => 1,
            ],
            'altImageUrl#2' => [
                'header'   => 'Alternative Image URL 2',
                'name'     => 'altImageUrl',
                'number'   => 2,
            ],
            'altImageUrl#3' => [
                'header'   => 'Alternative Image URL 3',
                'name'     => 'altImageUrl',
                'number'   => 3,
            ],
            'altImageUrl#4' => [
                'header'   => 'Alternative Image URL 4',
                'name'     => 'altImageUrl',
                'number'   => 4,
            ],
            'altImageUrl#5' => [
                'header'   => 'Alternative Image URL 5',
                'name'     => 'altImageUrl',
                'number'   => 5,
            ],
            'mobileUrl' => [
                'header'   => 'Mobile URL',
            ],
            'relatedProducts' => [
                'header'   => 'Related Products',
            ],
            'merchandisingType' => [
                'header'   => 'Merchandising Type',
                'value'    => 'New',
            ],
            'productWeight' => [
                'header'   => 'Product Weight',
                'value'    => '',
            ],
            'zipShipFrom' => [
                'header'   => 'Zip Code',
                'value'    => '',
            ],
            'weight' => [
                'header'   => 'Shipping Weight',
            ],
            'weightUnits' => [
                'header'   => 'Weight Unit of Measure'
            ],
            'format' => [
                'header'   => 'Format',
                'mapped'   => 'ebaycomnet_format_field',
            ],
            'unitPrice' => [
                'header'   => 'Unit Price',
                'value'    => '',
            ],
            'isBundle' => [
                'header'   => 'Bundle',
                'value'    => 'No',
            ],
            'softwarePlatform' => [
                'header'   => 'Software Platform',
                'mapped'   => 'ebaycomnet_platform_field',
            ],
            'displayType' => [
                'header'   => 'Watch Display Type',
                'mapped'   => 'ebaycomnet_display_field',
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

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->ebaycomnet_skip_out_of_stock) {
            $qb->andWhere('(p.amount > :zero) OR (p.inventoryEnabled = 0)')
                ->setParameter('zero', 0);
        }

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->ebaycomnet_skip_no_category) {
            $qb->linkInner('p.eBayCommerceCategory');
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
        return substr(parent::getNameColumnValue($column, $item), 0, 88);
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
        return substr(parent::getDescriptionColumnValue($column, $item), 0, 3998);
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
        $category = $item->getEBayCommerceCategory();

        return $category ? $category->getName() : '';
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
        $category = $item->getEBayCommerceCategory();

        return $category ? $category->getId() : '';
    }

    /**
     * Get value for the Shipping Rate column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getShippingColumnValue(array $column, FeedItem $item)
    {
        return $item->getFreeShipping ? '0' : $this->mapProductField(
            \XLite\Core\Config::getInstance()->QSL->ProductFeeds->ebaycomnet_shipping_field,
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
     * Get value for the Size Units column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getSizeUnitsColumnValue(array $column, FeedItem $item)
    {
        return 'Inches';
    }

    /**
     * Get value for the Weight Units column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getWeightUnitsColumnValue(array $column, FeedItem $item)
    {
        return 'Pounds';
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
        $date = parent::getLaunchDateColumnValue($column, $item);

        return $date ? date('Ymd', $date) : '';
    }

    /**
     * Get suffix for URLs included into the feed.
     *
     * @return string
     */
    protected function getUrlSuffix()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->ebaycomnet_url_suffix;
    }

    /**
     * Check whether variant attributes should be included into the product name.
     *
     * @return boolean
     */
    protected function isProductNameWithAttributes()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->ebaycomnet_variant_names;
    }

    /**
     * Get value for the category column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getPriceColumnValue(array $column, FeedItem $item)
    {
        return $item->getNetPrice(); // or $item->getClearPrice();
    }
}
