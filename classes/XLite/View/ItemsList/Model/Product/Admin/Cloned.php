<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product\Admin;

/**
 * Cloned products
 */
class Cloned extends \XLite\View\ItemsList\Model\Product\Admin\AAdmin
{
    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_PRICE  = 'p.price';
    public const SORT_BY_MODE_NAME   = 'translations.name';
    public const SORT_BY_MODE_SKU    = 'p.sku';
    public const SORT_BY_MODE_AMOUNT = 'p.amount';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_PRICE  => 'Price',
            static::SORT_BY_MODE_NAME   => 'Name',
            static::SORT_BY_MODE_SKU    => 'SKU',
            static::SORT_BY_MODE_AMOUNT => 'Amount',
        ];

        parent::__construct($params);
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['cloned_products']);
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'cloned_products';
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/product/style.css';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku'   => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('SKU'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Product\SKU',
                static::COLUMN_ORDERBY => 100,
            ],
            'name'  => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Product\Name',
                static::COLUMN_MAIN    => true,
                static::COLUMN_ORDERBY => 200,
            ],
            'price' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Price'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Price',
                static::COLUMN_PARAMS  => ['min' => 0],
                static::COLUMN_ORDERBY => 300,
            ],
            'qty'   => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Stock'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Integer\ProductQuantity',
                static::COLUMN_ORDERBY => 400,
            ],
        ];
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array_merge(parent::getListNameSuffixes(), ['cloned']);
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Product\Admin\Cloned';
    }

    // {{{ Search

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    public function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{\XLite\Model\Repo\Product::P_ORDER_BY} = $this->getOrderBy();

        $result->{\XLite\Model\Repo\Product::P_SUBSTRING} = '[ clone ]';
        $result->{\XLite\Model\Repo\Product::P_BY_TITLE}  = 'Y';

        return $result;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Product')->search($cnd, $countOnly);
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_NAME;
    }

    // }}}

    // {{{ Content helpers

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $class = parent::getColumnClass($column, $entity);

        if ($column[static::COLUMN_CODE] == 'qty' && !$entity->getInventoryEnabled()) {
            $class .= ' infinity';
        }

        return $class;
    }

    /**
     * Check - has specified column attention or not
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return boolean
     */
    protected function hasColumnAttention(array $column, \XLite\Model\AEntity $entity = null)
    {
        return parent::hasColumnAttention($column, $entity)
            || ($column[static::COLUMN_CODE] == 'qty' && $entity && $entity->isLowLimitReached());
    }

    // }}}

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCrossIcon()
    {
        return true;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Get right actions templates name
     *
     * @return array
     */
    protected function getRightActions()
    {
        $list = parent::getRightActions();

        array_unshift(
            $list,
            'items_list/product/table/parts/columns/action.edit.twig'
        );

        return $list;
    }

    /**
     * Checks if this itemslist is exportable through 'Export all' button
     *
     * @return boolean
     */
    protected function isExportable()
    {
        return true;
    }

    // }}}
}
