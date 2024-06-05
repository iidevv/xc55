<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Abstract admin model-based items list (table)
 */
abstract class Table extends \XLite\View\ItemsList\Model\AModel implements ProviderInterface
{
    public const COLUMN_NAME          = 'name';
    public const COLUMN_TEMPLATE      = 'template';
    public const COLUMN_HEAD_TEMPLATE = 'headTemplate';
    public const COLUMN_HEAD_HELP     = 'headHelp';
    public const COLUMN_SUBHEADER     = 'subheader';
    public const COLUMN_CLASS         = 'class';
    public const COLUMN_CODE          = 'code';
    public const COLUMN_LINK          = 'link';
    public const COLUMN_METHOD_SUFFIX = 'methodSuffix';
    public const COLUMN_CREATE_CLASS  = 'createClass';
    public const COLUMN_CREATE_TEMPLATE = 'createTemplate';
    public const COLUMN_MAIN          = 'main';
    public const COLUMN_SERVICE       = 'service';
    public const COLUMN_PARAMS        = 'params';
    public const COLUMN_SORT          = 'sort';
    public const COLUMN_SEARCH_WIDGET = 'searchWidget';
    public const COLUMN_NO_WRAP       = 'noWrap';
    public const COLUMN_EDIT_ONLY     = 'editOnly';
    public const COLUMN_SELECTOR      = 'columnSelector';
    public const COLUMN_REMOVE        = 'columnRemove';
    public const COLUMN_ORDERBY       = 'orderBy';
    public const COLUMN_EDIT_LINK     = 'editLink';
    public const COLUMN_NO_HEAD       = 'noHead';
    public const COLUMN_HEAD_COLSPAN  = 'headColspan';

    /**
     * Widget param names
     */
    public const PARAM_WRAP_WITH_FORM = 'wrapWithForm';

    /**
     * Columns (local cache)
     *
     * @var array
     */
    protected $columns;

    /**
     * Main column index
     *
     * @var integer
     */
    protected $mainColumn;

    /**
     * Define columns structure
     *
     * @return array
     */
    abstract protected function defineColumns();

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_WRAP_WITH_FORM => new \XLite\Model\WidgetParam\TypeBool(
                'Wrap with form',
                $this->wrapWithFormByDefault()
            ),
        ];
    }

    /**
     * Get this object
     *
     * @return \XLite\View\ItemsList\Model\Table
     */
    protected function getItemsListObject()
    {
        return $this;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function isWrapWithForm()
    {
        return $this->getParam(static::PARAM_WRAP_WITH_FORM);
    }

    /**
     * Default value for PARAM_WRAP_WITH_FORM
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return false;
    }

    /**
     * Return wrapper form options
     *
     * @return array
     */
    protected function getFormOptions()
    {
        return [
            'class'     => '\XLite\View\Form\ItemsList\AItemsList',
            'name'      => str_replace('\\', '', get_class($this)),
            'target'    => $this->getFormTarget(),
            'action'    => $this->getFormAction(),
            'params'    => $this->getFormParams()
        ];
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'simple_items_list_controller';
    }

    /**
     * Get wrapper form action
     *
     * @return string
     */
    protected function getFormAction()
    {
        return 'updateItemsList';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return [
            'itemsList' => get_class($this),
        ];
    }

    /**
     * Sorting helper for uasort
     *
     * @param array $column1
     * @param array $column2
     *
     * @return int
     */
    public function sortColumnsByOrder($column1, $column2)
    {
        $column1[static::COLUMN_ORDERBY] = $column1[static::COLUMN_ORDERBY] ?? 0;
        $column2[static::COLUMN_ORDERBY] = $column2[static::COLUMN_ORDERBY] ?? 0;

        return $column1[static::COLUMN_ORDERBY] > $column2[static::COLUMN_ORDERBY] ? 1 : -1;
    }

    /**
     * The columns are ordered according the static::COLUMN_ORDERBY values
     *
     * @return array
     */
    protected function prepareColumns()
    {
        $columns = $this->defineColumns();
        $index = 100;
        foreach ($columns as $i => $v) {
            if (!isset($v[static::COLUMN_ORDERBY])) {
                $columns[$i][static::COLUMN_ORDERBY] = $index;
                $index += 100;
            }
        }

        uasort($columns, [$this, 'sortColumnsByOrder']);

        return $columns;
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/style.less';
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/style.css';

        if ($this->getSortableType() === static::SORT_TYPE_MOVE) {
            $list = array_merge(
                $list,
                $this->getWidget([], $this->getMovePositionWidgetClassName())->getCSSFiles(),
                $this->getWidget([], $this->getOrderByWidgetClassName())->getCSSFiles()
            );
        }

        return $list;
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/controller.js';

        if ($this->getSortableType() === static::SORT_TYPE_MOVE) {
            $list = array_merge(
                $list,
                $this->getWidget([], $this->getMovePositionWidgetClassName())->getJSFiles(),
                $this->getWidget([], $this->getOrderByWidgetClassName())->getJSFiles()
            );
        }

        return $list;
    }

    /**
     * Return true if items list should be displayed in static mode (no editable widgets, no controls)
     *
     * @return boolean
     */
    protected function isStatic()
    {
        return false;
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return parent::isPagerVisible()
            && $this->getPager()->isVisible();
    }

    /**
     * Get preprocessed columns structure
     *
     * @return array
     */
    protected function getColumns()
    {
        if (!isset($this->columns)) {
            $this->columns = [];

            if ($this->getLeftActions()) {
                $this->columns[] = $this->getLeftActionsColumn();
            }

            foreach ($this->prepareColumns() as $idx => $column) {
                $column[static::COLUMN_CODE] = $idx;
                $column[static::COLUMN_METHOD_SUFFIX]
                    = \Includes\Utils\Converter::convertToUpperCamelCase($column[static::COLUMN_CODE]);
                if (!isset($column[static::COLUMN_TEMPLATE]) && !isset($column[static::COLUMN_CLASS])) {
                    $column[static::COLUMN_TEMPLATE] = 'items_list/model/table/field.twig';
                }
                $column[static::COLUMN_PARAMS] = $column[static::COLUMN_PARAMS] ?? [];
                $column[static::COLUMN_NO_HEAD] = false;
                $column[static::COLUMN_HEAD_COLSPAN] = 1;

                $this->columns[] = $column;
            }

            if ($this->getEditLink()) {
                $this->columns[] = [
                    static::COLUMN_CODE     => 'edit-link',
                    static::COLUMN_NAME     => '',
                    static::COLUMN_TEMPLATE => 'items_list/model/table/parts/edit_link.twig',
                    static::COLUMN_SERVICE  => true,
                    static::COLUMN_LINK     => $this->getEditLink(),
                ];
            }

            if ($this->getRightActions()) {
                $this->columns[] = [
                    static::COLUMN_CODE     => 'actions right',
                    static::COLUMN_NAME     => '',
                    static::COLUMN_TEMPLATE => 'items_list/model/table/right_actions.twig',
                    static::COLUMN_SERVICE  => true,
                    static::COLUMN_REMOVE   => $this->isRemoved(),
                ];
            }

            $this->assignColspanHeaders($this->columns);
        }

        return $this->columns;
    }

    /**
     * @param $columns array
     */
    protected function assignColspanHeaders(&$columns)
    {
        foreach ($this->getColspanHeaders() as $code => $union) {
            $index = $this->getColumnIndex($columns, $code);

            $columns = $this->assignColspanHeader($columns, $index, $union);
        }
    }

    /**
     * @param array    $columns
     * @param int      $index
     * @param string[] $union
     *
     * @return array
     */
    protected function assignColspanHeader($columns, $index, $union): array
    {
        $columns = $this->assignColspanHeaderLeft($columns, $index, $union);
        $columns = $this->assignColspanHeaderRight($columns, $index, $union);

        return $columns;
    }

    /**
     * @param array    $columns
     * @param int      $index
     * @param string[] $union
     *
     * @return array
     */
    protected function assignColspanHeaderLeft($columns, $index, $union): array
    {
        $columnIndex = $index;
        while (--$columnIndex >= 0) {
            if (in_array($columns[$columnIndex][static::COLUMN_CODE], $union, true)) {
                $columns[$columnIndex][static::COLUMN_NO_HEAD] = true;
                $columns[$index][static::COLUMN_HEAD_COLSPAN]++;
            } else {
                break;
            }
        }

        return $columns;
    }

    /**
     * @param array    $columns
     * @param int      $index
     * @param string[] $union
     *
     * @return array
     */
    protected function assignColspanHeaderRight($columns, $index, $union): array
    {
        $columnIndex = $index;
        while (++$columnIndex <= count($columns)) {
            if (in_array($columns[$columnIndex][static::COLUMN_CODE], $union, true)) {
                $columns[$columnIndex][static::COLUMN_NO_HEAD] = true;
                $columns[$index][static::COLUMN_HEAD_COLSPAN]++;
            } else {
                break;
            }
        }

        return $columns;
    }

    /**
     * @param array  $columns
     * @param string $code
     *
     * @return int|null
     */
    protected function getColumnIndex($columns, $code): ?int
    {
        foreach ($columns as $index => $column) {
            if ($column[static::COLUMN_CODE] === $code) {
                return (int) $index;
            }
        }

        return null;
    }

    /**
     * Define columns with same header
     * Format: ['main_column_code' => ['merged_column_1', 'merged_column_2', 'merged_column_3']]
     *
     * @return array
     */
    protected function getColspanHeaders()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getLeftActionsColumn()
    {
        $result = [
            static::COLUMN_CODE     => 'actions left',
            static::COLUMN_NAME     => '',
            static::COLUMN_TEMPLATE => 'items_list/model/table/left_actions.twig',
            static::COLUMN_SERVICE  => true,
            static::COLUMN_HEAD_TEMPLATE => $this->getSortType() === static::SORT_TYPE_INPUT
                ? 'items_list/model/table/parts/pos_input.twig'
                : ($this->getSortType() === static::SORT_TYPE_MOVE
                    ? 'items_list/model/table/parts/pos_move.twig'
                    : ''
                ),
            static::COLUMN_SELECTOR => $this->isSelectable(),
        ];

        if ($this->isPositionSortable()) {
            $result[static::COLUMN_SORT] = $this->getSortableDefaultSortBy();
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isPositionSortable()
    {
        return false;
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if (
            !$this->isPositionSortable()
            && in_array($this->getSortableType(), [static::SORT_TYPE_MOVE, static::SORT_TYPE_INPUT], true)
        ) {
            unset($this->widgetParams[static::PARAM_SORT_BY]);
        }
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        $result = parent::getSortByModeDefault();

        if ($this->sortByModes) {
            $default = $this->getSortableDefaultSortBy();
            if (array_key_exists($default, $this->sortByModes)) {
                $result = $default;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getSortableDefaultSortBy()
    {
        $repo = $this->getRepository();

        return $repo->getDefaultAlias() . '.' . $this->getSortFieldName();
    }

    /**
     * Return columns count
     *
     * @return integer
     */
    protected function getColumnsCount()
    {
        return count($this->getColumns());
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isTableHeaderVisible()
    {
        $result = false;
        foreach ($this->getColumns() as $column) {
            if (!empty($column['name'])) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check if at least one of headers has subheader
     *
     * @return boolean
     */
    protected function hasSubheaders()
    {
        foreach ($this->getColumns() as $column) {
            if (!empty($column[static::COLUMN_SUBHEADER])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getTableTagClassString()
    {
        return implode(' ', $this->getTableTagClasses());
    }

    /**
     * @return array
     */
    protected function getTableTagClasses()
    {
        $result = [
            'list'
        ];

        if (!$this->hasResults()) {
            $result[] = 'list-no-items';
        }

        return $result;
    }

    /**
     * Get main column
     *
     * @return array
     */
    protected function getMainColumn()
    {
        $columns = $this->getColumns();

        if (!isset($this->mainColumn)) {
            $result = null;
            $first = null;

            foreach ($columns as $i => $column) {
                if (!isset($column[static::COLUMN_SERVICE]) || !$column[static::COLUMN_SERVICE]) {
                    if (!isset($first)) {
                        $first = $i;
                    }
                    if (isset($column[static::COLUMN_MAIN]) && $column[static::COLUMN_MAIN]) {
                        $result = $i;
                        break;
                    }
                }
            }

            $this->mainColumn = $result ?? $first;
        }

        return $columns[$this->mainColumn] ?? null;
    }

    /**
     * Check - specified column is main or not
     *
     * @param array $column Column
     *
     * @return boolean
     */
    protected function isMainColumn(array $column)
    {
        $main = $this->getMainColumn();

        return $main && $column[static::COLUMN_CODE] == $main[static::COLUMN_CODE];
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getColumnValue(array $column, \XLite\Model\AEntity $entity)
    {
        $suffix = $column[static::COLUMN_METHOD_SUFFIX];

        // Getter
        $method = 'get' . $suffix . 'ColumnValue';
        $value = method_exists($this, $method)
            ? $this->$method($entity)
            : $this->getEntityValue($entity, $column[static::COLUMN_CODE]);

        // Preprocessing
        $method = 'preprocess' . \Includes\Utils\Converter::convertToUpperCamelCase($column[static::COLUMN_CODE]);
        if (method_exists($this, $method)) {
            // $method assembled frm 'preprocess' + field name
            $value = $this->$method($value, $column, $entity);
        }

        return $value;
    }

    /**
     * Get entity value
     *
     * @param \XLite\Model\AEntity $entity Entity object
     * @param string               $name   Property name
     *
     * @return mixed
     */
    protected function getEntityValue($entity, $name)
    {
        $result = null;

        $method = 'get' . \Includes\Utils\Converter::convertToUpperCamelCase($name);

        if (method_exists($entity, $method)) {
            // $method assembled frm 'get' + field name
            $result = $entity->$method();
        } elseif ($entity->isPropertyExists($name)) {
            $result = $entity->$name;
        }

        return $result;
    }

    /**
     * Get field objects list (only inline-based form fields)
     *
     * @return array
     */
    protected function getFieldObjects()
    {
        $list = [];

        foreach ($this->getColumns() as $column) {
            $name = $column[static::COLUMN_CODE];
            if (
                isset($column[static::COLUMN_CLASS])
                && is_subclass_of($column[static::COLUMN_CLASS], 'XLite\View\FormField\Inline\AInline')
            ) {
                $params = $column[static::COLUMN_PARAMS] ?? [];
                $list[] = [
                    'class'      => $column[static::COLUMN_CLASS],
                    'parameters' => ['fieldName' => $name, 'fieldParams' => $params],
                ];
            }
        }

        if ($this->isSwitchable()) {
            $cell = $this->getSwitcherField();
            $list[] = [
                'class'      => $cell['class'],
                'parameters' => ['fieldName' => $cell['name'], 'fieldParams' => $cell['params']],
            ];
        }

        if ($this->getSortType() != static::SORT_TYPE_NONE) {
            $cell = $this->getSortField();
            $list[] = [
                'class'      => $cell['class'],
                'parameters' => ['fieldName' => $cell['name'], 'fieldParams' => $cell['params']],
            ];
        }

        foreach ($list as $i => $class) {
            $list[$i] = new $class['class']($class['parameters']);
        }

        return $list;
    }

    /**
     * Get switcher field
     *
     * @return array
     */
    protected function getSwitcherField()
    {
        return [
            'class'  => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\Enabled',
            'name'   => 'enabled',
            'params' => [],
        ];
    }

    /**
     * Get sort field
     *
     * @return array
     */
    protected function getSortField()
    {
        return $this->getSortType() == static::SORT_TYPE_INPUT
            ? [
                'class'  => $this->getOrderByWidgetClassName(),
                'name'   => 'position',
                'params' => [],
            ]
            :
            [
                'class'  => $this->getMovePositionWidgetClassName(),
                'name'   => 'position',
                'params' => [],
            ];
    }

    /**
     * Defines the position MOVE widget class name
     *
     * @return string
     */
    protected function getMovePositionWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\Move';
    }

    /**
     * Defines the position OrderBy widget class name
     *
     * @return string
     */
    protected function getOrderByWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\OrderBy';
    }

    /**
     * Get create field classes
     *
     * @return array
     */
    protected function getCreateFieldClasses()
    {
        $list = [];

        foreach ($this->getColumns() as $column) {
            $name = $column[static::COLUMN_CODE];
            $class = null;
            if (
                isset($column[static::COLUMN_CREATE_CLASS])
                && is_subclass_of($column[static::COLUMN_CREATE_CLASS], 'XLite\View\FormField\Inline\AInline')
            ) {
                $class = $column[static::COLUMN_CREATE_CLASS];
            } elseif (
                isset($column[static::COLUMN_CLASS])
                && is_subclass_of($column[static::COLUMN_CLASS], 'XLite\View\FormField\Inline\AInline')
            ) {
                $class = $column[static::COLUMN_CLASS];
            }

            if ($class) {
                $params = $column[static::COLUMN_PARAMS] ?? [];
                $list[] = [
                    'class'      => $class,
                    'parameters' => [
                        \XLite\View\FormField\Inline\AInline::PARAM_FIELD_NAME   => $name,
                        \XLite\View\FormField\Inline\AInline::PARAM_FIELD_PARAMS => $params,
                        \XLite\View\FormField\Inline\AInline::PARAM_EDIT_ONLY    => true,
                    ],
                ];
            }
        }

        foreach ($list as $i => $class) {
            $list[$i] = new $class['class']($class['parameters']);
        }

        return $list;
    }

    /**
     * Get create line columns
     *
     * @return array
     */
    protected function getCreateColumns()
    {
        $columns = [];

        if ($this->getLeftActions()) {
            $columns[] = [
                static::COLUMN_CODE     => 'actions left',
                static::COLUMN_NAME     => '',
                static::COLUMN_SERVICE  => true,
                static::COLUMN_TEMPLATE => 'items_list/model/table/parts/empty_left.twig',
            ];
        }

        foreach ($this->prepareColumns() as $idx => $column) {
            if (
                (isset($column[static::COLUMN_CREATE_CLASS]) && $column[static::COLUMN_CREATE_CLASS])
                || (isset($column[static::COLUMN_CLASS]) && $column[static::COLUMN_CLASS])
            ) {
                // By class
                $column[static::COLUMN_CODE] = $idx;
                $column[static::COLUMN_METHOD_SUFFIX]
                    = \Includes\Utils\Converter::convertToUpperCamelCase($column[static::COLUMN_CODE]);
                if (!isset($column[static::COLUMN_CREATE_CLASS]) || !$column[static::COLUMN_CREATE_CLASS]) {
                    $column[static::COLUMN_CREATE_CLASS] = $column[static::COLUMN_CLASS];
                }
                $columns[] = $column;
            } elseif (!empty($column[static::COLUMN_CREATE_TEMPLATE])) {
                // By template
                $columns[] = [
                    static::COLUMN_CODE     => $idx,
                    static::COLUMN_TEMPLATE => $column[static::COLUMN_CREATE_TEMPLATE],
                ];
            } else {
                // Empty
                $columns[] = [
                    static::COLUMN_CODE     => $idx,
                    static::COLUMN_TEMPLATE => 'items_list/model/table/empty.twig',
                ];
            }
        }

        if ($this->getEditLink()) {
            $columns[] = [
                static::COLUMN_CODE     => 'edit-link',
                static::COLUMN_NAME     => '',
                static::COLUMN_TEMPLATE => 'items_list/model/table/parts/empty.twig',
                static::COLUMN_SERVICE  => true,
            ];
        }

        if ($this->getRightActions()) {
            $columns[] = [
                static::COLUMN_CODE     => 'actions right',
                static::COLUMN_NAME     => '',
                static::COLUMN_SERVICE  => true,
                static::COLUMN_TEMPLATE => $this->isRemoved()
                    ? 'items_list/model/table/parts/remove_create.twig'
                    : 'items_list/model/table/parts/empty_right.twig',
            ];
        }

        return $columns;
    }

    /**
     * List has top creation box
     *
     * @return boolean
     */
    protected function isTopInlineCreation()
    {
        return $this->isInlineCreation() === static::CREATE_INLINE_TOP;
    }

    /**
     * List has bottom creation box
     *
     * @return boolean
     */
    protected function isBottomInlineCreation()
    {
        return $this->isInlineCreation() === static::CREATE_INLINE_BOTTOM;
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
     * Get cell list name part
     *
     * @param string $type   Cell type
     * @param array  $column Column
     *
     * @return string
     */
    protected function getCellListNamePart($type, array $column)
    {
        return $type . '.' . str_replace(' ', '.', $column[static::COLUMN_CODE]);
    }

    // {{{ Content helpers

    /**
     * Get service name for this itemslist
     *
     * @return string
     */
    public function getIdentifierClass()
    {
        return strtolower(
            implode('-', $this->getViewClassKeys())
        );
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $class = parent::getContainerClass()
            . ' items-list-table'
            . ($this->isTableHeaderVisible() ? ' no-thead' : '');

        $class .= ' ' . $this->getIdentifierClass();

        if (
            !$this->isPositionSortable()
            || $this->isPositionSort($this->getSortBy())
        ) {
            $class .= ' position-sort';
        } else {
            $class .= ' visual-sort';
        }

        return trim($class);
    }

    /**
     * @param $sortBy
     *
     * @return bool
     */
    protected function isPositionSort($sortBy)
    {
        return $sortBy === $this->getSortableDefaultSortBy();
    }

    protected function getSortOrder()
    {
        return $this->isPositionSort($this->getSortBy())
            ? static::SORT_ORDER_ASC
            : parent::getSortOrder();
    }

    /**
     * Get head class
     *
     * @param array $column Column
     *
     * @return string
     */
    protected function getHeadClass(array $column)
    {
        return $column[static::COLUMN_CODE];
    }

    /**
     * @param array $column
     * @return bool|mixed
     */
    protected function isNoColumnHead(array $column)
    {
        return $column[static::COLUMN_NO_HEAD] ?? false;
    }

    /**
     * @param array $column
     * @return int|mixed
     */
    protected function getColumnHeadColspan(array $column)
    {
        return $column[static::COLUMN_HEAD_COLSPAN] ?? 1;
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
        return 'cell '
            . $column[static::COLUMN_CODE]
            . ($this->hasColumnAttention($column, $entity) ? ' attention' : '')
            . ($this->isMainColumn($column) ? ' main' : '')
            . ($this->isEditLinkEnabled($column, $entity) ? ' has-edit-link' : '')
            . (empty($column[static::COLUMN_NO_WRAP]) ? '' : ' no-wrap');
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
        return false;
    }

    /**
     * Get action cell class
     *
     * @param integer $i        Cell index
     * @param string  $template Template
     *
     * @return string
     */
    protected function getActionCellClass($i, $template)
    {
        return 'action' . (0 < $i ? ' next' : '');
    }

    // }}}

    // {{{ Top / bottom behaviors

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions = [];

        if (!$this->isStatic()) {
            if (
                $this->isCreation() == static::CREATE_INLINE_TOP
                && $this->getCreateURL()
                && $this->isInlineCreation() == static::CREATE_INLINE_NONE
            ) {
                $actions[] = 'items_list/model/table/parts/create.twig';
            } elseif ($this->isInlineCreation() == static::CREATE_INLINE_TOP) {
                $actions[] = 'items_list/model/table/parts/create_inline.twig';
            }
        }

        return $actions;
    }

    /**
     * Get bottom actions
     *
     * @return array
     */
    protected function getBottomActions()
    {
        $actions = [];

        if (!$this->isStatic()) {
            if (
                $this->isCreation() == static::CREATE_INLINE_BOTTOM
                && $this->getCreateURL()
                && $this->isInlineCreation() == static::CREATE_INLINE_NONE
            ) {
                $actions[] = 'items_list/model/table/parts/create.twig';
            } elseif ($this->isInlineCreation() == static::CREATE_INLINE_BOTTOM) {
                $actions[] = 'items_list/model/table/parts/create_inline.twig';
            }
        }

        return $actions;
    }

    // }}}

    // {{{ Line bahaviors

    /**
     * Return sort type
     *
     * @return integer
     */
    protected function getSortType()
    {
        return ($this->getSortableType() === static::SORT_TYPE_MOVE && 1 < $this->getPager()->getPagesCount())
            ? static::SORT_TYPE_INPUT
            : $this->getSortableType();
    }

    /**
     * Get left actions tempaltes
     *
     * @return array
     */
    protected function getLeftActions()
    {
        $list = [];

        if (!$this->isStatic()) {
            if ($this->getSortType() === static::SORT_TYPE_MOVE) {
                $list[] = $this->getMoveActionTemplate();
            } elseif ($this->getSortType() === static::SORT_TYPE_INPUT) {
                $list[] = $this->getPositionActionTemplate();
            }

            if ($this->isSelectable()) {
                $list[] = $this->getSelectorActionTemplate();
            }

            if ($this->isSwitchable()) {
                $list[] = $this->getSwitcherActionTemplate();
            }

            if ($this->isDefault()) {
                $list[] = $this->getDefaultActionTemplate();
            }
        }

        return $list;
    }

    /**
     * Template for position action definition
     *
     * @return string
     */
    protected function getPositionActionTemplate()
    {
        return 'items_list/model/table/parts/position.twig';
    }

    /**
     * Template for move action definition
     *
     * @return string
     */
    protected function getMoveActionTemplate()
    {
        return 'items_list/model/table/parts/move.twig';
    }

    /**
     * Return sort field name for tag
     *
     * @return string
     */
    protected function getSortFieldName()
    {
        return 'position';
    }

    /**
     * Template for selector action definition
     *
     * @return string
     */
    protected function getSelectorActionTemplate()
    {
        return 'items_list/model/table/parts/selector.twig';
    }

    /**
     * Template for switcher action definition
     *
     * @return string
     */
    protected function getSwitcherActionTemplate()
    {
        return 'items_list/model/table/parts/switcher.twig';
    }

    /**
     * Template for default action definition
     *
     * @return string
     */
    protected function getDefaultActionTemplate()
    {
        return 'items_list/model/table/parts/default.twig';
    }

    /**
     * Get title for 'default' action
     *
     * @return string
     */
    protected function getDefaultActionTitle()
    {
        return static::t('Default');
    }

    /**
     * Template for default action definition
     *
     * @return string
     */
    protected function getRemoveActionTemplate()
    {
        return 'items_list/model/table/parts/remove.twig';
    }

    /**
     * @return bool
     */
    public function isCrossIcon()
    {
        return false;
    }

    /**
     * Check if row selected
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return bool
     */
    protected function isRowSelected($entity)
    {
        return false;
    }

    /**
     * Get right actions templates
     *
     * @return array
     */
    protected function getRightActions()
    {
        $list = [];

        if (!$this->isStatic() && $this->isRemoved()) {
            $list[] = $this->getRemoveActionTemplate();
        }

        return $list;
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return $entity->isPersistent();
    }

    /**
     * Check - switch entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntitySwitch(\XLite\Model\AEntity $entity)
    {
        return (bool)$this->getSwitcherField();
    }

    /**
     * Check - is entity selectable or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntitySelect(\XLite\Model\AEntity $entity)
    {
        return true;
    }

    // }}}

    // {{{ Inherited methods

    /**
     * Check - body tempalte is visible or not
     *
     * @return boolean
     */
    protected function isPageBodyVisible()
    {
        return parent::isPageBodyVisible() || $this->isHeadSearchVisible();
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return 0 < count($this->getTopActions());
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return 0 < count($this->getBottomActions());
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'model/table.twig';
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return parent::getPageBodyDir() . '/table';
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        return $this->isAllowEntityRemove($entity) && parent::removeEntity($entity);
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return boolean
     */
    protected function isEmptyListTemplateVisible()
    {
        return true;
    }

    // }}}

    // {{{ Head sort

    /**
     * Check - specified column is sorted or not
     *
     * @param array $column Column
     *
     * @return boolean
     */
    protected function isColumnSorted(array $column)
    {
        $field = $this->getSortBy();

        return !empty($column[static::COLUMN_SORT]) && $field == $column[static::COLUMN_SORT];
    }

    /**
     * Get next sort direction
     *
     * @param array $column Column
     *
     * @return string
     */
    protected function getSortDirectionNext(array $column)
    {
        if ($this->isColumnSorted($column)) {
            $direction = $this->getSortOrder() == static::SORT_ORDER_DESC
                ? static::SORT_ORDER_ASC
                : static::SORT_ORDER_DESC;
        } else {
            $direction = $this->getSortOrder() ?: static::SORT_ORDER_DESC;
        }

        return $direction;
    }

    /**
     * Get sort link class
     *
     * @param array $column Column
     *
     * @return string
     */
    protected function getSortLinkClass(array $column)
    {
        $classes = 'sort';

        if ($this->isColumnSorted($column)) {
            $classes .= ' current-sort ' . $this->getSortOrder() . '-direction';
        }

        return $classes;
    }

    // }}}

    // {{{ Head search

    /**
     * Check - search-in-head mechanism is available or not
     *
     * @return boolean
     */
    protected function isHeadSearchVisible()
    {
        $found = false;

        foreach ($this->getColumns() as $column) {
            if ($this->isSearchColumn($column)) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * Check - specified column has search widget or not
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isSearchColumn(array $column)
    {
        return !empty($column[static::COLUMN_SEARCH_WIDGET]);
    }


    /**
     * Get search cell class
     *
     * @param array $column Column info
     *
     * @return string
     */
    protected function getSearchCellClass(array $column)
    {
        return 'search-cell ' . $column[static::COLUMN_CODE] . ' '
            . ($this->isSearchColumn($column) ? 'filled' : 'empty');
    }

    // }}}

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isTemplateColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        return !empty($column[static::COLUMN_TEMPLATE]);
    }

    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        return !isset($column[static::COLUMN_TEMPLATE]);
    }

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array $column Column
     *
     * @return boolean
     */
    protected function isCreateTemplateColumnVisible(array $column)
    {
        return !isset($column[static::COLUMN_CREATE_CLASS]);
    }

    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column Column
     *
     * @return boolean
     */
    protected function isCreateClassColumnVisible(array $column)
    {
        return isset($column[static::COLUMN_CREATE_CLASS]);
    }

    /**
     * Return true if 'Edit' link should be displayed in column line
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isEditLinkEnabled(array $column, \XLite\Model\AEntity $entity)
    {
        return !empty($column[static::COLUMN_EDIT_LINK]);
    }

    /**
     * Get edit link params string
     * @todo: reorder params
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return string
     */
    protected function getEditLinkAttributes(\XLite\Model\AEntity $entity, array $column)
    {
        return '';
    }

    /**
     * Get label for 'Edit' link
     *
     * @param \XLite\Model\AEntity $entity
     *
     * @return string
     */
    protected function getEditLinkLabel($entity)
    {
        return '';
    }

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
        return isset($column[static::COLUMN_LINK]);
    }

    /**
     * Get JS handler class name (used for pagination)
     *
     * @return string
     */
    protected function getJSHandlerClassName()
    {
        return 'TableItemsList';
    }

    /**
     * Prepare field params for
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return array
     */
    protected function preprocessFieldParams(array $column, \XLite\Model\AEntity $entity)
    {
        return $column[static::COLUMN_PARAMS];
    }

    /**
     * @return array
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Do you really want to delete selected items?' => static::t('Do you really want to delete selected items?')
        ];
    }
}
