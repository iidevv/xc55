<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestFeed\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;
use XC\ProductVariants\Model\ProductVariant;

/**
 * Class generating feed files for Printerest comparison shopping website.
 *
 * See:
 * - https://help.pinterest.com/en/business/article/before-you-get-started-with-catalogs
 */
class Pinterest extends \QSL\ProductFeeds\Logic\FeedGenerator\ABase
{
    /**
     * Get the feed filename.
     *
     * @return string
     */
    public function getFeedFilename()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->pinterest_feed_name;
    }

    /**
     * Get the minimum number of hours that should pass between automatic feed updates.
     *
     * @return integer
     */
    public function getAutoRefreshDelay()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->pinterest_refresh_rate;
    }

    /**
     * Define information on CSV columns (key - machine name, value - header)
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'id'             => [
                'header'   => 'id',
                'required' => true,
            ],
            'name'           => [
                'header'   => 'title',
                'required' => true,
            ],
            'productUrl'     => [
                'header'   => 'link',
                'required' => true,
            ],
            'description'    => [
                'header'   => 'description',
                'required' => true,
            ],
            'imageUrl'       => [
                'header'   => 'image_link',
                'required' => true,
            ],
            'price'          => [
                'header'   => 'price',
                'required' => true,
            ],
            'inStock'        => [
                'header'   => 'availability',
                'required' => true,
            ],
            'pinterestCategory' => [
                'header'   => 'google_product_category'
            ],
            'itemGroupId'  => [
                'header' => 'item_group_id',
            ],
            'variantNames'  => [
                'header' => 'variant_names',
            ],
            'variantValues' => [
                'header' => 'variant_values',
            ],
            'altImageUrls'   => [
                'header' => 'additional_image_link',
            ],
            'condition' => [
                'header'   => 'condition',
                'value'    => 'new',
            ],
            'brand' => [
                'header'   => 'brand',
                'mapped'   => 'pinterest_brand_field',
            ],
            'gtin' => [
                'header'   => 'gtin',
                'mapped'   => 'pinterest_gtin_field',
            ],
            'manufacturerParnN' => [
                'header'   => 'mpn',
                'mapped'   => 'pinterest_mpn_field',
            ],
            'gender' => [
                'header'   => 'gender',
                'mapped'   => 'pinterest_gender_field',
            ],
            'age' => [
                'header'   => 'age_group',
                'mapped'   => 'pinterest_age_field',
            ],
            'color' => [
                'header'   => 'color',
                'mapped'   => 'pinterest_color_field',
            ],
            'material' => [
                'header'   => 'material',
                'mapped'   => 'pinterest_material_field',
            ],
            'pattern' => [
                'header'   => 'pattern',
                'mapped'   => 'pinterest_pattern_field',
            ],
            'size' => [
                'header'   => 'size',
                'mapped'   => 'pinterest_size_field',
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

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->pinterest_skip_disabled) {
            $qb->andWhere('p.enabled = :enabled')
                ->setParameter('enabled', 1);
        }

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->pinterest_skip_out_of_stock) {
            $qb->andWhere('(p.amount > :zero) OR (p.inventoryEnabled = 0)')
                ->setParameter('zero', 0);
        }

        return $qb;
    }

    /**
     * Get value for the productUrl column.
     *
     * @param array                           $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getProductUrlColumnValue(array $column, FeedItem $item)
    {
        if ($productId = $item->getProduct()->getProductId()) {
            $params = ['product_id' => $productId];

            if ($variant = $item->getVariant()) {
                $params['variant_id'] = $variant->getId();
            }

            return $this->buildFeedUrl('product', '', $params);
        }

        return null;
    }

    /**
     * Get value for the id column.
     *
     * @param array                           $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getIdColumnValue(array $column, FeedItem $item)
    {
        if ($productId = $item->getProduct()->getProductId()) {
            if ($variant = $item->getVariant()) {
                return $productId . "_" . $variant->getId();
            }

            return $productId;
        }

        return null;
    }

    /**
     * Get value for the inStock column.
     *
     * @param array                           $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getInStockColumnValue(array $column, FeedItem $item)
    {
        return strtolower(parent::getInStockColumnValue($column, $item));
    }

    /**
     * Get value for the ItemGroupId column.
     *
     * @param array                           $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getItemGroupIdColumnValue(array $column, FeedItem $item)
    {
        if ($productId = $item->getProduct()->getProductId()) {
            if ($item->getVariant()) {
                return $productId;
            }
        }

        return null;
    }

    /**
     * Get value for the VariantNames column.
     *
     * @param array                           $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getVariantNamesColumnValue(array $column, FeedItem $item)
    {
        if ($item->getProduct() && $item->hasVariant()) {
            return implode(',', $this->getVariantAttributeNames($item));
        }

        return null;
    }

    /**
     * Get value for the VariantValues column.
     *
     * @param array                           $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getVariantValuesColumnValue(array $column, FeedItem $item)
    {
        if ($item->getProduct() && $item->hasVariant()) {
            return implode(',', $this->getVariantAttributeValues($item));
        }

        return null;
    }

    /**
     * Get value for the category column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getPinterestCategoryColumnValue(array $column, FeedItem $item)
    {
        $category = $item->getPinterestCategory();

        return $category ? $category->getName() : '';
    }

    /**
     * Get value for the price column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getPriceColumnValue(array $column, FeedItem $item)
    {
        if ($product = $item->getProduct()) {

            /** @var ProductVariant $variant */
            if ($variant = $item->getVariant()) {
                return $variant->getDisplayPrice();
            }

            return $product->getDisplayPrice();
        }

        return null;
    }
}
