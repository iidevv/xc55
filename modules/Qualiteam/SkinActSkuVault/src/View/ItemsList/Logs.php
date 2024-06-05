<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\ItemsList;

use Qualiteam\SkinActSkuVault\Model\Log;
use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\OperationTypes;
use Qualiteam\SkinActSkuVault\Model\Repo\Log as LogRepo;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use XLite\Core\CommonCell;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\View\ItemsList\Model\Table;
use XLite\View\SearchPanel\SimpleSearchPanel;

class Logs extends Table
{
    public const PARAM_DIRECTION  = 'direction';
    public const PARAM_STATUS     = 'status';
    public const PARAM_OPERATION  = 'operation';
    public const PARAM_DATE_RANGE = 'dateRange';

    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_DIRECTION = 'l.direction';
    public const SORT_BY_MODE_STATUS    = 'l.status';
    public const SORT_BY_MODE_OPERATION = 'l.operation';
    public const SORT_BY_MODE_DATE      = 'l.date';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_DIRECTION => 'Direction',
            static::SORT_BY_MODE_STATUS    => 'Status',
            static::SORT_BY_MODE_OPERATION => 'Operation',
            static::SORT_BY_MODE_DATE      => 'Date',
        ];

        parent::__construct($params);
    }

    /**
     * @inheritDoc
     */
    protected function defineColumns()
    {
        return [
            'direction' => [
                static::COLUMN_NAME    => static::t('Direction'),
                static::COLUMN_ORDERBY => 100,
                static::COLUMN_SORT    => static::SORT_BY_MODE_DIRECTION,
            ],
            'status'    => [
                static::COLUMN_NAME    => static::t('Status'),
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_SORT    => static::SORT_BY_MODE_STATUS,
            ],
            'operation' => [
                static::COLUMN_NAME    => static::t('Operation'),
                static::COLUMN_ORDERBY => 300,
                static::COLUMN_SORT    => static::SORT_BY_MODE_OPERATION,
            ],
            'message'   => [
                static::COLUMN_NAME    => static::t('Message'),
                static::COLUMN_ORDERBY => 400,
                static::COLUMN_MAIN    => true,
            ],
            'date'      => [
                static::COLUMN_NAME    => static::t('Date'),
                static::COLUMN_ORDERBY => 500,
                static::COLUMN_SORT    => static::SORT_BY_MODE_DATE,
            ],
        ];
    }

    protected function preprocessDate($value)
    {
        return Converter::formatTime($value);
    }

    protected function preprocessOperation($value)
    {
        $types = OperationTypes::TYPES;
        return array_key_exists($value, $types) ? $types[$value] : $value;
    }

    protected function preprocessDirection($value)
    {
        $directions = Directions::DIRECTIONS;
        return array_key_exists($value, $directions) ? $directions[$value] : $value;
    }

    protected function preprocessStatus($value)
    {
        $statuses = SyncStatuses::STATUSES;
        return array_key_exists($value, $statuses) ? $statuses[$value] : $value;
    }

    protected function defineRepositoryName()
    {
        return Log::class;
    }

    /**
     * Mark list as selectable
     *
     * @return bool
     */
    protected function isSelectable()
    {
        return true;
    }

    protected function getRightActions()
    {
        return [];
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DATE;
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }

    /**
     * Get panel class
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return \Qualiteam\SkinActSkuVault\View\StickyPanel\Logs::class;
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return SimpleSearchPanel::class;
    }

    /**
     * @param CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(CommonCell $cnd, $countOnly = false)
    {
        return Database::getRepo(Log::class)->search($cnd, $countOnly);
    }

    /**
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            LogRepo::P_DIRECTION  => static::PARAM_DIRECTION,
            LogRepo::P_STATUS     => static::PARAM_STATUS,
            LogRepo::P_OPERATION  => static::PARAM_OPERATION,
            LogRepo::P_DATE_RANGE => static::PARAM_DATE_RANGE,
        ];
    }

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DIRECTION  => new \XLite\Model\WidgetParam\TypeString('Direction', ''),
            static::PARAM_STATUS     => new \XLite\Model\WidgetParam\TypeString('Status', ''),
            static::PARAM_OPERATION  => new \XLite\Model\WidgetParam\TypeString('Operation', ''),
            static::PARAM_DATE_RANGE => new \XLite\Model\WidgetParam\TypeString('Date range', ''),
        ];
    }

}
