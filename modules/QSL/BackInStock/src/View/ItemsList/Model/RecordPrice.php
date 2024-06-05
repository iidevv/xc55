<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\ItemsList\Model;

/**
 * Records items list (price)
 */
class RecordPrice extends \QSL\BackInStock\View\ItemsList\Model\ARecord
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['back_in_stock_record_prices']);
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/BackInStock/record_prices/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        return [
            'product' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Product'),
                static::COLUMN_SORT => 'ptranslations.name',
                static::COLUMN_LINK => 'product',
            ],
            'profile' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Customer'),
                static::COLUMN_SORT => 'profile.login',
                static::COLUMN_LINK => 'profile',
            ],
            'price' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Price'),
                static::COLUMN_SORT => 'r.price',
                static::COLUMN_HEAD_HELP => \XLite\Core\Translation::lbl('Desired price'),
            ],
            'date' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Date'),
                static::COLUMN_SORT => 'r.date',
            ],
            'state' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Record state'),
                static::COLUMN_SORT => 'r.state',
            ],
            'backDate' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Price drop date'),
                static::COLUMN_SORT => 'r.backDate',
            ],
            'sentDate' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Sent date'),
                static::COLUMN_SORT => 'r.sentDate',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineRepositoryName()
    {
        return 'QSL\BackInStock\Model\RecordPrice';
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'back_in_stock_record_prices';
    }

    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return [
            'target' => 'back_in_stock_record_prices'
        ];
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\QSL\BackInStock\View\SearchPanel\RecordPrice\Admin\Main';
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return 'QSL\BackInStock\View\StickyPanel\ItemsList\RecordPrice';
    }

    /**
     * Preprocess price value
     *
     * @param integer                                    $value  Value
     * @param array                                      $column Column data
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return string
     */
    protected function preprocessPrice($value, array $column, \QSL\BackInStock\Model\ARecord $entity)
    {
        return $value
            ? implode('', \XLite::getInstance()->getCurrency()->formatParts($value))
            : static::t('n/a');
    }

    public function isSearchVisible(): bool
    {
        return true;
    }
}
