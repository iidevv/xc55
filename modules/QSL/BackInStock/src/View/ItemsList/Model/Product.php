<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\ItemsList\Model;

/**
 * Products items list
 */
class Product extends \QSL\BackInStock\View\ItemsList\Model\AProduct
{
    public const SORT_BY_RECORDS_WAITING_COUNT = 'records_waiting_count';
    public const SORT_BY_RECORDS_SENT_COUNT    = 'records_sent_count';

    /**
     * @inheritdoc
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_RECORDS_WAITING_COUNT => 'Waiting records count',
            static::SORT_BY_RECORDS_SENT_COUNT    => 'Sent record count',
        ];

        parent::__construct($params);
    }

    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        return ['back_in_stock_products'];
    }

    /**
     * @inheritdoc
     */
    protected function getFormTarget()
    {
        return 'back_in_stock_products';
    }

    /**
     * @inheritdoc
     */
    public function getSearchFormOptions()
    {
        $list = parent::getSearchFormOptions();
        $list['target'] = 'back_in_stock_products';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Product::SEARCH_BACK_IN_STOCK} = true;

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        // Remove redundant columns
        foreach ($columns as $k => $v) {
            if (!in_array($k, ['sku', 'name', 'qty'])) {
                unset($columns[$k]);
            } elseif ($k === 'qty') {
                unset($columns[$k][static::COLUMN_CLASS]);
            }
        }

        $columns['records_waiting_count'] = [
            static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Waiting records count'),
            static::COLUMN_SORT    => static::SORT_BY_RECORDS_WAITING_COUNT,
            static::COLUMN_ORDERBY => 1000,
        ];
        $columns['records_sent_count'] = [
            static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Sent records count'),
            static::COLUMN_SORT    => static::SORT_BY_RECORDS_SENT_COUNT,
            static::COLUMN_ORDERBY => 1100,
        ];

        return $columns;
    }

    /**
     * Get records count (waiting)
     *
     * @param \XLite\Model\Product $entity Product
     *
     * @return integer
     */
    protected function getRecordsWaitingCountColumnValue(\XLite\Model\Product $entity)
    {
        return $entity->countBack2StockWaitingRecords();
    }

    /**
     * Get records count (sent)
     *
     * @param \XLite\Model\Product $entity Product
     *
     * @return integer
     */
    protected function getRecordsSentCountColumnValue(\XLite\Model\Product $entity)
    {
        return $entity->countBack2StockSentRecords();
    }

    /**
     * Get sticky panel class
     */
    protected function getPanelClass()
    {
        return 'QSL\BackInStock\View\StickyPanel\ItemsList\SingleSettingLink';
    }

    /**
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'data-widget' => 'QSL\BackInStock\View\ItemsList\Model\Product'
        ];
    }
}
