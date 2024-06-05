<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\ItemsList\Model;

use QSL\ProductQuestions\Model\Repo\Question as Repo;

/**
 * Questions items list
 */
class Question extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    public const PARAM_SEARCH_PUBLISHED = 'published';

    /**
     * Sort modes
     *
     * @var array
     */
    protected $sortByModes = [
        'q.date'       => 'Date',
        'q.published'  => 'Replied',
        'q.private'    => 'Type',
    ];

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'items_list/model/table/style.css';
        $list[] = 'modules/QSL/ProductQuestions/questions/style.css';

        return $list;
    }


    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_SEARCH_PUBLISHED => new \XLite\Model\WidgetParam\TypeCheckbox('Published', false),
        ];
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'question' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Question'),
                static::COLUMN_ORDERBY  => 100,
                static::COLUMN_LINK     => 'product_question',
                static::COLUMN_NO_WRAP  => true,
            ],
            'state' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Replied'),
                static::COLUMN_ORDERBY => 150,
                static::COLUMN_TEMPLATE => 'modules/QSL/ProductQuestions/questions/cell.state.twig',
                static::COLUMN_SORT     => 'q.published',
            ],
            'product' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Product'),
                static::COLUMN_ORDERBY  => 200,
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_TEMPLATE => 'modules/QSL/ProductQuestions/questions/cell.product.twig',
            ],
            'profile' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Customer'),
                static::COLUMN_ORDERBY  => 250,
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_TEMPLATE => 'modules/QSL/ProductQuestions/questions/cell.profile.twig',
            ],
            'date' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Date'),
                static::COLUMN_ORDERBY  => 300,
                static::COLUMN_TEMPLATE => 'modules/QSL/ProductQuestions/questions/cell.date.twig',
                static::COLUMN_SORT     => 'q.date',
            ],
            'private' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Type'),
                static::COLUMN_ORDERBY => 350,
                static::COLUMN_TEMPLATE => 'modules/QSL/ProductQuestions/questions/cell.type.twig',
                static::COLUMN_SORT     => 'q.private',
            ],
        ];
    }

    /**
     * Get column value for the "product" column.
     *
     * @param array   $data Data
     * @param string  $name Column name
     * @param integer $i    Subcolumn index
     *
     * @return string
     */
    protected function getProductColumnValue(array $data, $name, $i)
    {
        return $data->getProduct();
    }

    /**
     * Format 'date' field value
     *
     * @param mixed  $value Value
     * @param array  $data  Data
     * @param string $name  Column name
     *
     * @return string
     */
    protected function formatDateColumnValue($value, array $data, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'QSL\ProductQuestions\Model\Question';
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('product_question');
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array
     */
    protected function getOrderBy()
    {
        $sortBy = $this->getSortBy();
        $dateSortBy = Repo::SEARCH_ORDERBY_DATE;

        if ($sortBy !== $dateSortBy) {
            // Add the default sort column as the second ordering parameter
            $result = [
                [$sortBy, $this->getSortOrder()],
                [$dateSortBy, \XLite\View\ItemsList\AItemsList::SORT_ORDER_ASC],
            ];
        } else {
            $result = [$sortBy, $this->getSortOrder()];
        }

        return $result;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return Repo::SEARCH_ORDERBY_PUBLISHED;
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New question';
    }

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

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' product-questions';
    }

    /**
     * Get panel class
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return 'QSL\ProductQuestions\View\StickyPanel\ItemsList\Question';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }


    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        $params = [];

        if (static::PARAM_SEARCH_PUBLISHED) {
            $params[Repo::SEARCH_PUBLISHED] = static::PARAM_SEARCH_PUBLISHED;
        }

        return $params;
    }

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();
        $result->{\QSL\ProductQuestions\Model\Repo\Question::P_ORDER_BY} = $this->getOrderBy();

        return $result;
    }

    // }}}
}
