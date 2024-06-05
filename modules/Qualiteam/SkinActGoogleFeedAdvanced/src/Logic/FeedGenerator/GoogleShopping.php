<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class GoogleShopping extends \QSL\ProductFeeds\Logic\FeedGenerator\GoogleShopping
{
    /**
     * Only added to google feed products should be in the feed
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function applyFeedSettings(\XLite\Model\QueryBuilder\AQueryBuilder $qb)
    {
        $qb = parent::applyFeedSettings($qb);

        $qb
            ->andWhere('p.add_to_google_feed = :add_to_google_feed')
            ->setParameter('add_to_google_feed', true);

        return $qb;
    }

    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['condition']['mapped'] = 'googleshop_condition_field';
        $columns['googleCategory']['mapped'] = 'googleshop_googlecategory_field';
        $columns['category']['mapped'] = 'googleshop_producttype_field';

        return $columns;
    }

    protected function getCategoryColumnValue(array $column, FeedItem $item)
    {
        $value = $this->getMappedField($column, $item);

        return $value !== '' ? $value : parent::getCategoryColumnValue($column, $item);
    }

    protected function getConditionColumnValue(array $column, FeedItem $item)
    {
        $value = $this->getMappedField($column, $item);

        return $value !== '' ? $value : $column['value'];
    }

    protected function getGoogleCategoryColumnValue(array $column, FeedItem $item)
    {
        $value = $this->getMappedField($column, $item);

        return $value !== '' ? $value : parent::getGoogleCategoryColumnValue($column, $item);
    }
}
