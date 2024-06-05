<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;
use XLite\Model\Currency;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\Query\Expr\Andx;

/**
 * Class generating feed files for Google Shopping comparison shopping website.
 *
 * See:
 * - https://services.google.com/fh/files/misc/en-productlistingads-feedguide.pdf
 * - https://support.google.com/merchants/answer/188494
 * - https://support.google.com/merchants/answer/160081
 */
class GoogleShopping extends ABase
{
    /**
     * Get the feed filename.
     *
     * @return string
     */
    public function getFeedFilename()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_feed_name;
    }

    /**
     * Get the minimum number of hours that should pass between automatic feed updates.
     *
     * @return integer
     */
    public function getAutoRefreshDelay()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_refresh_rate;
    }

    /**
     * Define information on CSV columns (key - machine name, value - header)
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'id' => [
                'header'   => 'id',
                'required' => true,
                'mapped'   => 'googleshop_id_field',
            ],
            'familyId' => [
                'header'   => 'item group id',
                'required' => true,
                'mapped'   => 'googleshop_family_id_field',
            ],
            'name' => [
                'header' => 'title',
                'required' => true,
            ],
            'description' => [
                'header'   => 'description',
            ],
            'googleCategory' => [
                'header'   => 'google product category',
                'required' => true,
            ],
            'category' => [
                'header'   => 'product type',
                'required' => true,
            ],
            'productUrl' => [
                'header'   => 'link',
                'required' => true,
            ],
            // TODO: For apparel items, it is required that you provide a unique image of the specific variant if the item differs by ‘color’, ‘pattern’ or ‘material’.
            'imageUrl' => [
                'header'   => 'image link',
            ],
            'altImageUrls' => [
                'header'    => 'additional image link',
                'delimiter' => ',',
            ],
            'condition' => [
                'header'   => 'condition',
                'value'    => 'new',
            ],
            'inStock' => [
                'header'   => 'availability',
            ],
            'price' => [
                'header'   => 'price',
                'required' => true,
            ],
            // See https://support.google.com/merchants/answer/1196048
            'salePrice' => [
                'header'   => 'sale price',
                'required' => true,
                'value'    => '',  // TODO: sale price
            ],
            // See https://support.google.com/merchants/answer/1196048
            'salePeriod' => [
                'header'   => 'sale price effective date',
                'required' => true,
                'value'    => '',  // TODO: sale period
            ],
            // US only (https://support.google.com/merchants/answer/188494?hl=en)
            'unitPricingMeasure' => [
                'header'   => 'unit pricing measure',
                'mapped'   => 'googleshop_price_measure_field',
            ],
            // US only (https://support.google.com/merchants/answer/188494?hl=en)
            'unitPricingBaseMeasure' => [
                'header'   => 'unit pricing base measure',
                'mapped'   => 'googleshop_pbase_measure_field',
            ],
            'brand' => [
                'header'   => 'brand',
                'mapped'   => 'googleshop_brand_field',
            ],
            'gtin' => [
                'header'   => 'gtin',
                'mapped'   => 'googleshop_gtin_field',
            ],
            'manufacturerParnN' => [
                'header'   => 'mpn',
                'mapped'   => 'googleshop_mpn_field',
            ],
            'hasIdentifier' => [
                'header'   => 'identifier exists', // TODO: has identifier?
                'value'    => 'TRUE',
            ],
            'gender' => [
                'header'   => 'gender',
                'mapped'   => 'googleshop_gender_field',
            ],
            'age' => [
                'header'   => 'age group',
                'mapped'   => 'googleshop_age_field',
            ],
            'color' => [
                'header'   => 'color',
                'mapped'   => 'googleshop_color_field',
            ],
            'material' => [
                'header'   => 'material',
                'mapped'   => 'googleshop_material_field',
            ],
            'size' => [
                'header'   => 'size',
                'mapped'   => 'googleshop_size_field',
            ],
            'pattern' => [
                'header'   => 'pattern',
                'mapped'   => 'googleshop_pattern_field',
            ],
            'tax' => [
                'header'   => 'tax',
                'value'    => '', // TODO: google shopping: tax
            ],
            'shipping' => [
                'header'   => 'shipping',
            ],
            'poundWeight' => [
                'header'   => 'shipping weight',
            ],
            'multipack' => [
                'header'   => 'multipack',
                'value'    => '',
            ],
            'adult' => [
                'header'   => 'adult',
                'value'    => '', // TODO: google shopping: adult
                'matches'  => '/^(TRUE|FALSE)$/'
            ],
            'adwordsGrouping' => [
                'header'   => 'adwords grouping',
                'value'    => '',
            ],
            'adwordsLabels' => [
                'header'   => 'adwords labels',
                'value'    => '',
            ],
            'adwordsRedirect' => [
                'header'   => 'adwords redirect',
                'value'    => '',
            ],
            'energyEfficiencyClass' => [
                'header'   => 'energy efficiency class',
                'value'    => '',
            ],
            'loyaltyPoints' => [
                'header'    => 'loyalty points',
                'value'     => '',
                'countries' => ['JP'], // TODO: limit fields to countries
            ],
            'excludedDestination' => [
                'header'   => 'excluded destination',
                'value'    => '',
                'matches'  => '/^(Shopping|Commerce Search)$/', // TODO: check for allowed values
            ],
            'expirationDate' => [
                'header'   => 'expiration date',
                'value'    => '',
                'matches'  => '/^\d{4}-\d{2}-\d{2}$/',
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
        return '';
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

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_skip_out_of_stock) {
            // shamelessly copied from XC\ProductVariants\Model\Repo\Product::prepareCndInventoryIn()
            // to ensure consistency
            if (\Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'ProductVariants')) {
                $qb->linkLeft('p.variants', 'pv');
                $orCnd = new Orx([
                    'p.inventoryEnabled = :disabled',
                    'pv.amount > :zero',
                    new Andx([
                        'p.amount > :zero',
                        new Orx([
                            'pv.id IS NULL',
                            'pv.defaultAmount = true'
                        ])
                    ]),
                ]);
                $qb->andWhere($orCnd)
                    ->setParameter('disabled', false)
                    ->setParameter('zero', 0);
            } else {
                // the original non-variants-aware code
                $qb->andWhere('(p.amount > :zero) OR (p.inventoryEnabled = 0)')
                    ->setParameter('zero', 0);
            }
        }

        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_skip_no_category) {
            $qb->linkInner('p.googleShoppingCategory');
        }

        return $qb;
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
        return strtolower(parent::getInStockColumnValue($column, $item));
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
        return substr(parent::getDescriptionColumnValue($column, $item), 0, 9998);
    }

    /**
     * Get value for the category column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getGoogleCategoryColumnValue(array $column, FeedItem $item)
    {
        $category = $item->getGoogleShoppingCategory();

        return $category ? $category->getName() : '';
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
     * Get value for the category column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getPriceColumnValue(array $column, FeedItem $item)
    {
        // For the US, don't include tax in the price. For all other countries except Canada
        // and India, value added tax (VAT) has to be included in the price.
        // The price must include a currency according to ISO 4217 Standard.
        $companyLocation = \XLite\Core\Config::getInstance()->Company->locationCountry->getCode();
        $noVat = in_array($companyLocation, ['US', 'CA', 'IN'])
            || ! \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('CDev', 'VAT');
        $price = $noVat ? $item->getNetPrice() : $item->getVatPrice();

        return $this->formatFeedPrice($price);
    }

    /**
     * Return variant attribute values joined into a single string to add to product name.
     *
     * @param \QSL\ProductFeeds\Core\FeedItem $item Feed item.
     *
     * @return string
     */
    protected function getVariantAttributeValuesString(FeedItem $item)
    {
        return '- ' . implode(' ', $this->getVariantAttributeValues($item));
    }

    /**
     * Get suffix for URLs included into the feed.
     *
     * @return string
     */
    protected function getUrlSuffix()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_url_suffix;
    }

    /**
     * Check whether variant attributes should be included into the product name.
     *
     * @return boolean
     */
    protected function isProductNameWithAttributes()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_variant_names;
    }

    /**
     * Get value for the category column.
     *
     * @see https://support.google.com/merchants/answer/6324484?hl=en
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getShippingColumnValue(array $column, FeedItem $item)
    {
        return $item->getShippable()
            ? ''
            // For downloadable products we must return a zero shipping price
            : $this->getShippingColumnPrefix() . $this->formatFeedPrice(0);
    }

    /**
     * Returns the first 3 sub-attributes for the "shipping" column:
     * "country:delivery area:service".
     *
     * @see https://support.google.com/merchants/answer/6324484?hl=en
     *
     * @return string
     */
    protected function getShippingColumnPrefix()
    {
        // Just skip all the optional sub-attributes.
        return ':::';
    }

    /**
     * Format price as Google Shopping feeds require.
     *
     * @param float                 $price    Price to format
     * @param \XLite\Model\Currency $currency Currency OPTIONAL
     *
     * @return string
     */
    protected function formatFeedPrice($price, Currency $currency = null)
    {
        $currency = $currency ?: \XLite::getInstance()->getCurrency();

        $parts = $currency->formatParts($price);
        unset($parts['prefix']);
        $parts['decimalDelimiter'] = '.';
        $parts['suffix'] = ' ' . $currency->getCode();

        return implode('', $parts);
    }

    /**
     * Get feed items for a product.
     *
     * @param \XLite\Model\Product $product Product to export.
     *
     * @return array
     */
    protected function getFeedItems(\XLite\Model\Product $product)
    {
        $items = parent::getFeedItems($product);

        if ($this->shouldCheckVariants()) {
            // filter out out-of-stock variants:
            $inStockVaiants = [];
            foreach ($items as $item) {
                if (! $item->isOutOfStock()) {
                    $inStockVaiants[] = $item;
                }
            }

            return $inStockVaiants;
        }

        return $items;
    }

    protected function shouldCheckVariants()
    {
        static $result;

        if (! isset($result)) {
            $result = \XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_skip_out_of_stock
                && \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'ProductVariants');
        }

        return $result;
    }
}
