<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\ItemsList\Model;

use XCart\Extender\Mapping\ListChild;

/**
 * Reviews items list (common reviews page)
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Review extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_REVIEWER         = 'r.reviewerName';
    public const SORT_BY_MODE_RATING           = 'r.rating';
    public const SORT_BY_MODE_STATUS           = 'r.status';
    public const SORT_BY_MODE_ADDITION_DATE    = 'r.additionDate';

    /**
     * Widget param names
     */
    public const PARAM_SEARCH_DATE_RANGE   = 'dateRange';
    public const PARAM_SEARCH_KEYWORDS     = 'keywords';
    public const PARAM_SEARCH_RATING       = 'rating';
    public const PARAM_SEARCH_TYPE         = 'type';
    public const PARAM_SEARCH_STATUS       = 'status';

    /**
     * The product selector cache
     *
     * @var mixed
     */
    protected $productSelectorWidget = null;

    /**
     * The profile selector cache
     *
     * @var mixed
     */
    protected $profileSelectorWidget = null;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['reviews']);
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'XC\Reviews\View\SearchPanel\Review\Main';
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.review.blank');
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
     * @return string
     */
    protected function getFormTarget()
    {
        return 'reviews';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = [];

        $productId = \XLite\Core\Request::getInstance()->product_id;
        if ($productId) {
            $params['product_id'] = $productId;
        }

        return array_merge(
            parent::getFormParams(),
            $params
        );
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XC\Reviews\Model\Repo\Review::SEARCH_DATE_RANGE => static::PARAM_SEARCH_DATE_RANGE,
            \XC\Reviews\Model\Repo\Review::SEARCH_KEYWORDS   => static::PARAM_SEARCH_KEYWORDS,
            \XC\Reviews\Model\Repo\Review::SEARCH_RATING     => static::PARAM_SEARCH_RATING,
            \XC\Reviews\Model\Repo\Review::SEARCH_TYPE       => static::PARAM_SEARCH_TYPE,
            \XC\Reviews\Model\Repo\Review::SEARCH_STATUS     => static::PARAM_SEARCH_STATUS,
        ];
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Reviews/reviews/style.less';
        $list[] = 'modules/XC/Reviews/review/style.css';
        $list[] = 'modules/XC/Reviews/form_field/input/rating/rating.css';
        $list[] = 'vote_bar/vote_bar.css';

        $list = array_merge($list, $this->getProductSelectorWidget()->getCSSFiles());
        $list = array_merge($list, $this->getProfileSelectorWidget()->getCSSFiles());

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Reviews/form_field/input/rating/rating.js';

        $list = array_merge($list, $this->getProductSelectorWidget()->getJSFiles());
        $list = array_merge($list, $this->getProfileSelectorWidget()->getJSFiles());

        return $list;
    }

    /**
     * Getter of the product selector widget
     *
     * @return \XLite\View\FormField\Select\Model\ProductSelector
     */
    protected function getProductSelectorWidget()
    {
        if ($this->productSelectorWidget === null) {
            $this->productSelectorWidget = new \XLite\View\FormField\Select\Model\ProductSelector();
        }

        return $this->productSelectorWidget;
    }

    /**
     * Getter of the product selector widget
     *
     * @return \XLite\View\FormField\Select\Model\ProductSelector
     */
    protected function getProfileSelectorWidget()
    {
        if ($this->profileSelectorWidget === null) {
            $this->profileSelectorWidget = new \XLite\View\FormField\Select\Model\ProfileSelector();
        }

        return $this->profileSelectorWidget;
    }

    /**
     * Return profile id
     *
     * @param \XC\Reviews\Model\Review $entity
     *
     * @return int
     */
    public function getProfileId(\XC\Reviews\Model\Review $entity)
    {
        return $entity->getProfile()
            ? $entity->getProfile()->getProfileId()
            : 0;
    }

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
            static::SORT_BY_MODE_REVIEWER       => 'Reviewer',
            static::SORT_BY_MODE_RATING         => 'Rating',
            static::SORT_BY_MODE_STATUS         => 'Status',
            static::SORT_BY_MODE_ADDITION_DATE  => 'Addition date',
        ];

        parent::__construct($params);
    }

    // {{{ Search

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
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
            'modules/XC/Reviews/' . $this->getDir() . '/' . $this->getPageBodyDir() . '/review/action.link.twig'
        );

        return $list;
    }

    /**
     * Get search case (aggregated search conditions) processor
     * This should be passed in here by the controller, but i don't see appropriate way to do so
     *
     * @return \XLite\View\ItemsList\ISearchCaseProvider
     */
    public static function getSearchCaseProcessor()
    {
        return new \XLite\View\ItemsList\SearchCaseProcessor(
            static::getSearchParams(),
            static::getSearchValuesStorage()
        );
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if (is_string($paramValue)) {
                $paramValue = trim($paramValue);
            }

            if ($requestParam === static::PARAM_SEARCH_DATE_RANGE && is_array($paramValue)) {
                foreach ($paramValue as $i => $date) {
                    if (is_string($date) && strtotime($date) !== false) {
                        $paramValue[$i] = strtotime($date);
                    }
                }
            } elseif ($requestParam === static::PARAM_SEARCH_DATE_RANGE && $paramValue) {
                $paramValue = \XLite\View\FormField\Input\Text\DateRange::convertToArray($paramValue);
            }

            if ($paramValue !== '') {
                $result->$modelParam = $paramValue;
            }
        }

        $result->{\XC\Reviews\Model\Repo\Review::P_ORDER_BY} = $this->getOrderBy();

        // Comment this line to search reviews and ratingsXLite/View/Tooltip.php
        // $result->{\XC\Reviews\Model\Repo\Review::SEARCH_TYPE} =
        //    \XC\Reviews\Model\Repo\Review::SEARCH_TYPE_REVIEWS_ONLY;

        return $result;
    }

    // }}}

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_SEARCH_DATE_RANGE => new \XLite\Model\WidgetParam\TypeString('Date range', ''),
            static::PARAM_SEARCH_KEYWORDS => new \XLite\Model\WidgetParam\TypeString('Product, SKU or customer info', ''),
            static::PARAM_SEARCH_RATING => new \XLite\Model\WidgetParam\TypeString('Rating', ''),
            static::PARAM_SEARCH_TYPE => new \XLite\Model\WidgetParam\TypeString('Review type', ''),
            static::PARAM_SEARCH_STATUS => new \XLite\Model\WidgetParam\TypeString('Status', ''),
        ];
    }

    /**
     * Get column value for 'product' column
     *
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getProductColumnValue(\XC\Reviews\Model\Review $entity)
    {
        return $entity->getProduct()->getName();
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'product' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Product'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'product',
                static::COLUMN_ORDERBY  => 100,
            ],
            'reviewerName' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Reviewer'),
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/reviewer_info.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_REVIEWER,
                static::COLUMN_ORDERBY  => 200,
            ],
            'review' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Review'),
                static::COLUMN_MAIN     => true,
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/review.twig',
                static::COLUMN_ORDERBY  => 250,
            ],
            'rating' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Rating'),
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/rating.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_RATING,
                static::COLUMN_ORDERBY  => 300,
            ],
            'status' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Status'),
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/status.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_STATUS,
                static::COLUMN_ORDERBY  => 400,
            ],
            'additionDate' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Date'),
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/cell.date.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_ADDITION_DATE,
                static::COLUMN_ORDERBY  => 500,
            ],
        ];
    }

    /**
     * Preprocess addition date
     *
     * @param integer                               $date   Date
     * @param array                                 $column Column data
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function preprocessAdditionDate($date, array $column, \XC\Reviews\Model\Review $entity)
    {
        return $date
            ? \XLite\Core\Converter::getInstance()->formatTime($date)
            : static::t('Unknown');
    }

    /**
     * Return true if review is approved
     *
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return boolean
     */
    protected function isApproved(\XC\Reviews\Model\Review $entity)
    {
        return $entity->getStatus() == \XC\Reviews\Model\Review::STATUS_APPROVED;
    }

    /**
     * Return full review content (to display in tooltip)
     *
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getReviewFullContent(\XC\Reviews\Model\Review $entity)
    {
        return $entity->getReview();
    }

    /**
     * Return shortened review content
     *
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getReviewShortContent(\XC\Reviews\Model\Review $entity)
    {
        $review = $entity->getReview();
        $review = trim($review);

        if (function_exists('mb_substr')) {
            $value = mb_substr($review, 0, 30, 'utf-8');

            $result = $value
                . (
                    mb_strlen($value, 'utf-8') !== mb_strlen($review, 'utf-8')
                    ? '...'
                    : ''
                );
        } else {
            $value = substr($review, 0, 30);

            $result = $value
                . (
                    strlen($value) !== strlen($review)
                    ? '...'
                    : ''
                );
        }

        return func_htmlspecialchars($result);
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return true;
    }

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
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' reviews';
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XC\Reviews\View\StickyPanel\ItemsList\Review';
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

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XC\Reviews\Model\Review';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('review');
    }

    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        return parent::isLink($column, $entity) && (
                $column[static::COLUMN_CODE] !== 'product'
                || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog')
            );
    }

    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        if ($column[static::COLUMN_CODE] === 'product') {
            return \XLite\Core\Converter::buildURL(
                'product',
                '',
                ['product_id' => $entity->getProduct()->getProductId()]
            );
        }

        return parent::buildEntityURL($entity, $column);
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_ADDITION_DATE;
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return \XLite\View\ItemsList\AItemsList::SORT_ORDER_DESC;
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    public function isSearchVisible(): bool
    {
        return true;
    }
}
