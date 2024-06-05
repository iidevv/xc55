<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\ItemsList\Model;

/**
 * Returns items list
 */
class OrderReturn extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    public const PARAM_SEARCH_EMAIL       = 'customerEmail';
    public const PARAM_SEARCH_ORDER       = 'order';
    public const PARAM_SEARCH_ONLY_ISSUED = 'onlyIssued';

    /*
     * Sort params
     */
    public const SORT_BY_MODE_ORDER    = 'o.order';
    public const SORT_BY_MODE_DATE     = 'o.date';
    public const SORT_BY_MODE_REASON   = 'o.reason';
    public const SORT_BY_MODE_ACTION   = 'o.action';
    public const SORT_BY_MODE_STATUS   = 'o.status';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_ORDER    => 'Order #',
            static::SORT_BY_MODE_DATE     => 'Date',
            static::SORT_BY_MODE_REASON   => 'Reason',
            static::SORT_BY_MODE_ACTION   => 'Action',
            static::SORT_BY_MODE_STATUS   => 'Status',
        ];

        parent::__construct($params);
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/Returns/returns/style.css';

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
            static::PARAM_SEARCH_EMAIL       => new \XLite\Model\WidgetParam\TypeString('Customer email', ''),
            static::PARAM_SEARCH_ORDER       => new \XLite\Model\WidgetParam\TypeString('Order #', ''),
            static::PARAM_SEARCH_ONLY_ISSUED => new \XLite\Model\WidgetParam\TypeBool('Only issued', true),
        ];
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'order' => [
                static::COLUMN_NAME    => static::t('Order #'),
                static::COLUMN_LINK    => 'order',
                static::COLUMN_SORT    => static::SORT_BY_MODE_ORDER,
                static::COLUMN_ORDERBY => 200,
            ],
            'date' => [
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_LINK    => 'date',
                static::COLUMN_TEMPLATE => 'modules/QSL/Returns/returns/parts/date.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_DATE,
                static::COLUMN_ORDERBY  => 300,
            ],
            'items' => [
                static::COLUMN_NAME     => static::t('Items'),
                static::COLUMN_TEMPLATE => 'modules/QSL/Returns/returns/parts/items.twig',
                static::COLUMN_MAIN     => true,
                static::COLUMN_ORDERBY  => 400,
            ],
            'reason' => [
                static::COLUMN_NAME    => static::t('Reason'),
                static::COLUMN_SORT    => static::SORT_BY_MODE_REASON,
                static::COLUMN_ORDERBY => 500,
            ],
        ];

        if (\QSL\Returns\Main::isActionsEnabled()) {
            $columns['action'] = [
                static::COLUMN_NAME    => static::t('Action'),
                static::COLUMN_SORT    => static::SORT_BY_MODE_ACTION,
                static::COLUMN_ORDERBY => 600,
            ];
        }

        $columns['status'] = [
            static::COLUMN_NAME    => static::t('Status'),
            static::COLUMN_SORT    => static::SORT_BY_MODE_STATUS,
            static::COLUMN_ORDERBY => 700,
        ];

        return $columns;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'QSL\Returns\Model\OrderReturn';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('returns');
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
     * Get default sort mode
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_ORDER;
    }

    /**
     * Get default sort order
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' returns';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'QSL\Returns\View\StickyPanel\ItemsList\OrderReturn';
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \QSL\Returns\Model\Repo\OrderReturn::SEARCH_EMAIL       => static::PARAM_SEARCH_EMAIL,
            \QSL\Returns\Model\Repo\OrderReturn::SEARCH_ORDER       => static::PARAM_SEARCH_ORDER,
            \QSL\Returns\Model\Repo\OrderReturn::SEARCH_ONLY_ISSUED => static::PARAM_SEARCH_ONLY_ISSUED,
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

        $this->requestParams[] = static::PARAM_SEARCH_EMAIL;
        $this->requestParams[] = static::PARAM_SEARCH_ORDER;
        $this->requestParams[] = static::PARAM_SEARCH_ONLY_ISSUED;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\QSL\Returns\Model\Repo\OrderReturn::SEARCH_ORDER_BY} = $this->getOrderBy();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        if ($result->{\QSL\Returns\Model\Repo\OrderReturn::SEARCH_ONLY_ISSUED} === null) {
            $result->{\QSL\Returns\Model\Repo\OrderReturn::SEARCH_ONLY_ISSUED} = 1;
        }

        return $result;
    }

    // }}}

    /**
     * Check if the column must be a link.
     * It is used if the column field is displayed via
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        return parent::isLink($column, $entity)
        && ($column[static::COLUMN_CODE] !== 'order' || $this->hasLinkableOrder($entity));
    }

    /*
     * Check if transaction has order, which can be viewed by link
     *
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function hasLinkableOrder(\XLite\Model\AEntity $entity)
    {
        return $entity->getOrder()->getOrderNumber();
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
        switch ($column[static::COLUMN_LINK]) {
            case 'order':
                $result = \XLite\Core\Converter::buildURL(
                    $column[static::COLUMN_LINK],
                    '',
                    [
                        'order_number' => $entity->getOrder()->getOrderNumber(),
                        'page' => 'modify_return',
                    ]
                );

                break;

            default:
                $result = parent::buildEntityURL($entity, $column);

                break;
        }

        return $result;
    }

    /**
     * Get order
     *
     * @param \QSL\Returns\Model\OrderReturn $entity Order return
     *
     * @return string
     */
    protected function getOrderColumnValue(\QSL\Returns\Model\OrderReturn $entity)
    {
        /** @var \XLite\Model\Order $order */
        $order = $entity->getOrder();

        return $order
            ? $order->getPrintableOrderNumber()
            : '';
    }

    /**
     * Get reason
     *
     * @param \QSL\Returns\Model\OrderReturn $entity Order return
     *
     * @return string
     */
    protected function getReasonColumnValue(\QSL\Returns\Model\OrderReturn $entity)
    {
        $reason = $entity->getReason();

        return $reason
            // todo: switch to the multilingual getReasonName()
            ? $reason->getReason()
            : static::t('Other');
    }

    /**
     * Get action
     *
     * @param \QSL\Returns\Model\OrderReturn $entity Order return
     *
     * @return string
     */
    protected function getActionColumnValue(\QSL\Returns\Model\OrderReturn $entity)
    {
        $action = $entity->getAction();

        return $action
            // todo: switch to the multilingual getActionName()
            ? $action->getAction()
            : static::t('Other');
    }

    /**
     * Preprocess status
     *
     * @param integer              $value  Value
     * @param array                $column Column data
     * @param \QSL\Returns\Model\OrderReturn $entity Order return
     *
     * @return string
     */
    protected function preprocessStatus($value, array $column, \QSL\Returns\Model\OrderReturn $entity)
    {
        return $value
            ? $entity::getStatusName($value)
            : '';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return [
            'page'  => $this->getPage(),
            'id'    => \XLite\Core\Request::getInstance()->id
        ];
    }
}
