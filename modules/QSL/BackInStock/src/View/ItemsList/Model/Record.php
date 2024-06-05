<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\ItemsList\Model;

/**
 * Records items list (quantity)
 */
class Record extends \QSL\BackInStock\View\ItemsList\Model\ARecord
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['back_in_stock_records']);
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/BackInStock/records/style.css';

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
            'quantity' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Qty'),
                static::COLUMN_SORT => 'r.quantity',
                static::COLUMN_HEAD_HELP => \XLite\Core\Translation::lbl('Desired quantity'),
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
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Back date'),
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
        return 'QSL\BackInStock\Model\Record';
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'back_in_stock_records';
    }

    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return [
            'target' => 'back_in_stock_records'
        ];
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\QSL\BackInStock\View\SearchPanel\Record\Admin\Main';
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return 'QSL\BackInStock\View\StickyPanel\ItemsList\Record';
    }

    public function isSearchVisible(): bool
    {
        return true;
    }
}
