<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use XCart\Extender\Mapping\Extender;
use QSL\ProductFeeds\Core\FeedItem;

/**
 * Class generating feed files for Google Shopping comparison shopping website.
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Sale")
 */
class GoogleShoppingWithSale extends \QSL\ProductFeeds\Logic\FeedGenerator\GoogleShopping
{
    /**
     * Define information on CSV columns (key - machine name, value - header)
     *
     * @return array
     */
    protected function defineColumns()
    {
        $r = parent::defineColumns();

        // Map the "sale price" column to getSalePrice() method.
        unset($r['salePrice']['value']);

        return $r;
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
        if ($item->isProductOnSale()) {
            // For the US, don't include tax in the price. For all other countries except Canada
            // and India, value added tax (VAT) has to be included in the price.
            // The price must include a currency according to ISO 4217 Standard.
            $companyLocation = \XLite\Core\Config::getInstance()->Company->locationCountry->getCode();
            $noVat = in_array($companyLocation, ['US', 'CA', 'IN'])
                || ! \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('CDev', 'VAT');
            $price = $noVat ? $item->getNetPriceBeforeSale() : $item->getVatPriceBeforeSale();
            // $price .= ' ' . \XLite::getInstance()->getCurrency()->getCode();
            $price = $this->formatFeedPrice($price);
        } else {
            $price = parent::getPriceColumnValue($column, $item);
        }

        return $price;
    }

    /**
     * Get value for the category column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getSalePriceColumnValue(array $column, FeedItem $item)
    {
        return $item->isProductOnSale()
            // The default method returns the sale price already
            ? parent::getPriceColumnValue($column, $item)
            : '';
    }
}
