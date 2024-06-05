<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\ItemsList\Model;

/**
 * Surveys items list
 */
class Survey extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Allowed sort criterions
     */

    public const SORT_BY_MODE_ORDER_ID                 = 's.order';
    public const SORT_BY_MODE_FEEDBACK_DATE            = 's.feedbackDate';
    public const SORT_BY_MODE_STATUS                   = 's.status';
    public const SORT_BY_MODE_RATING                   = 's.rating';

    public const PARAM_SEARCH_DATE_RANGE   = 'dateRange';
    public const PARAM_SEARCH_KEYWORDS     = 'keywords';
    public const PARAM_SEARCH_RATING       = 'rating';
    public const PARAM_SEARCH_STATUS       = 'status';
    public const PARAM_SEARCH_ORDER_ID     = 'orderId';

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \QSL\CustomerSatisfaction\Model\Repo\Survey::SEARCH_DATE_RANGE => static::PARAM_SEARCH_DATE_RANGE,
            \QSL\CustomerSatisfaction\Model\Repo\Survey::SEARCH_KEYWORDS => static::PARAM_SEARCH_KEYWORDS,
            \QSL\CustomerSatisfaction\Model\Repo\Survey::SEARCH_RATING => static::PARAM_SEARCH_RATING,
            \QSL\CustomerSatisfaction\Model\Repo\Survey::SEARCH_STATUS => static::PARAM_SEARCH_STATUS,
        ];
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
            static::SORT_BY_MODE_ORDER_ID               => 'Order ID',
            static::SORT_BY_MODE_FEEDBACK_DATE          => 'Feedback added',
            static::SORT_BY_MODE_STATUS                 => 'Status',
            static::SORT_BY_MODE_RATING                 => 'Customer rating',
        ];

        parent::__construct($params);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CustomerSatisfaction/surveys/style.css';

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
            static::PARAM_SEARCH_DATE_RANGE => new \XLite\Model\WidgetParam\TypeString('Date range', ''),
            static::PARAM_SEARCH_KEYWORDS => new \XLite\Model\WidgetParam\TypeString('Survey Tags', ''),
            static::PARAM_SEARCH_RATING => new \XLite\Model\WidgetParam\TypeString('Rating', ''),
            static::PARAM_SEARCH_STATUS => new \XLite\Model\WidgetParam\TypeString('Status', ''),
        ];
    }

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
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'order' => [
                static::COLUMN_NAME          => \XLite\Core\Translation::lbl('Order #'),
                static::COLUMN_LINK          => 'order',
                static::COLUMN_SORT          => static::SORT_BY_MODE_ORDER_ID,
                static::COLUMN_ORDERBY       => 100,
            ],
            'user' => [
                static::COLUMN_NAME          => \XLite\Core\Translation::lbl('User'),
                static::COLUMN_LINK          => 'profile',
                static::COLUMN_MAIN          => true,
                static::COLUMN_ORDERBY       => 200,
            ],
            'feedbackDate' => [
                static::COLUMN_NAME          => \XLite\Core\Translation::lbl('Add Date'),
                static::COLUMN_SORT          => static::SORT_BY_MODE_FEEDBACK_DATE,
                static::COLUMN_ORDERBY       => 300,
            ],
            'status' => [
                static::COLUMN_NAME          => \XLite\Core\Translation::lbl('Status'),
                static::COLUMN_SORT          => static::SORT_BY_MODE_STATUS,
                static::COLUMN_ORDERBY       => 500,
            ],
            'rating' => [
                static::COLUMN_NAME          => \XLite\Core\Translation::lbl('Customer Rating'),
                static::COLUMN_TEMPLATE      => 'modules/XC/Reviews/reviews/cell/rating.twig',
                static::COLUMN_SORT          => static::SORT_BY_MODE_RATING,
                static::COLUMN_ORDERBY       => 700,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'QSL\CustomerSatisfaction\Model\Survey';
    }


    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return false;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return false;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
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
        return \XLite\Core\Converter::buildUrl('survey');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New survey';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' surveys';
    }


    // {{{ Search

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();


        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->{\QSL\CustomerSatisfaction\Model\Repo\Survey::SEARCH_ORDERBY} = $this->getOrderBy();
        if (!$result->{\QSL\CustomerSatisfaction\Model\Repo\Survey::SEARCH_STATUS}) {
            $result->{\QSL\CustomerSatisfaction\Model\Repo\Survey::SEARCH_STATUS} = '';
        }

        return $result;
    }

    /**
     * Preprocess Feedback date
     *
     * @param integer                               $date   Date
     * @param array                                 $column Column data
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function preprocessFeedbackDate($date, array $column, \QSL\CustomerSatisfaction\Model\Survey $entity)
    {
        return $date
            ? \XLite\Core\Converter::getInstance()->formatTime($date)
            : '';
    }

    /**
     * Get column value for 'Order #' column
     *
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getOrderColumnValue(\QSL\CustomerSatisfaction\Model\Survey $entity)
    {
        return $entity->getOrder()->getOrderNumber();
    }

    /**
     * Get column value for 'User' column
     *
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getUserColumnValue(\QSL\CustomerSatisfaction\Model\Survey $entity)
    {
        $profile = $entity->getOrder()->getOrigProfile() ?: $entity->getOrder()->getProfile();
        return $profile->getName() . ' (' . $profile->getLogin() . ')';
    }

    /**
     * Get column value for 'product' column
     *
     * @param \XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getStatusColumnValue(\QSL\CustomerSatisfaction\Model\Survey $entity)
    {
        return $entity->getStatusString();
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
        if ($column[static::COLUMN_CODE] == 'status') {
            if ($entity->getStatus() == \QSL\CustomerSatisfaction\Model\Survey::STATUS_NEW) {
                $class = $class . ' status-new';
            } elseif ($entity->getStatus() == \QSL\CustomerSatisfaction\Model\Survey::STATUS_IN_PROGRESS) {
                $class = $class . ' status-in-progress';
            }
        }
        return $class;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {

        if ($column[static::COLUMN_CODE] == 'order') {
            $result = \XLite\Core\Converter::buildURL(
                'order',
                '',
                ['order_number' => $entity->getOrder()->getOrderNumber()]
            );
        } elseif ($column[static::COLUMN_CODE] == 'user') {
            $profile = $entity->getOrder()->getOrigProfile() ?: $entity->getOrder()->getProfile();
            $result = \XLite\Core\Converter::buildURL(
                'profile',
                '',
                ['profile_id' => $profile->getProfileId()]
            );
        } else {
            $result = parent::buildEntityURL($entity, $column);
        }

        return $result;
    }

    /**
     * Get right actions templates name
     *
     * @return array
     */
    protected function getRightActions()
    {
        $list = parent::getRightActions();

        $list[] = 'modules/QSL/CustomerSatisfaction/' . $this->getDir() . '/' . $this->getPageBodyDir() . '/survey/action.link.twig';

        return $list;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_FEEDBACK_DATE;
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return \XLite\View\ItemsList\AItemsList::SORT_ORDER_ASC;
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array
     */
    protected function getOrderBy()
    {
        return [$this->getSortBy(), $this->getSortOrder()];
    }

    // }}}
}
