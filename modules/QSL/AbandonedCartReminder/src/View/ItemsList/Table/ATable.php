<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\ItemsList\Table;

/**
 * ItemsList abstract widget for data with rows representing aggregate data on
 * multiple entities. The list uses the same template that is used by the
 * ItemsList\Model widget.
 */
abstract class ATable extends \XLite\View\ItemsList\AItemsList
{
    public const COLUMN_CODE          = 'code';
    public const COLUMN_METHOD_SUFFIX = 'methodSuffix';
    public const COLUMN_TEMPLATE      = 'template';
    public const COLUMN_CLASS         = 'class';
    public const COLUMN_PARAMS        = 'params';
    public const COLUMN_NAME          = 'name';
    public const COLUMN_NO_WRAP       = 'noWrap';
    public const COLUMN_LINK          = 'link';

    /**
     * Highlight step.
     *
     * @var integer
     */
    protected $highlightStep = 2;

    /**
     * Request data.
     *
     * @var array
     */
    protected $requestData;

    /**
     * Columns (local cache).
     *
     * @var array
     */
    protected $columns;

    /**
     * Raw page data (local cache).
     *
     * @var array
     */
    protected $rawPageData;

    /**
     * Define columns structure.
     *
     * @return array
     */
    abstract protected function defineColumns();

    /**
     * Retrieves models stores them.
     *
     * @return array
     */
    abstract protected function initRawPageData();

    /**
     * Get anchor name.
     *
     * @return string
     */
    public function getAnchorName()
    {
        return implode('_', $this->getViewClassKeys());
    }

    /**
     * Get a list of CSS files.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/style.css';

        return $list;
    }

    /**
     * Get request data.
     *
     * @return array
     */
    protected function getRequestData()
    {
        if (!isset($this->requestData)) {
            $this->requestData = $this->defineRequestData();
        }

        return $this->requestData;
    }

    /**
     * Define request data.
     *
     * @return array
     */
    protected function defineRequestData()
    {
        return \XLite\Core\Request::getInstance()->getData();
    }

    /**
     * Check whether there are rows in the table.
     *
     * @return boolean
     */
    protected function hasResults()
    {
        return 0 < $this->getItemsCount();
    }

    /**
     * Check whether the body template is visible.
     *
     * @return boolean
     */
    protected function isPageBodyVisible()
    {
        return $this->hasResults();
    }

    /**
     * Check - table header is visible or not.
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return 0 < count($this->getTopActions());
    }

    /**
     * Get top actions.
     *
     * @return array
     */
    protected function getTopActions()
    {
        return [];
    }

    /**
     * Check whether the footer section is visible.
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return 0 < count($this->getBottomActions());
    }

    /**
     * Get bottom actions.
     *
     * @return array
     */
    protected function getBottomActions()
    {
        return [];
    }

    /**
     * Check - sticky panel is visible or not.
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return $this->getPanelClass() && $this->isPageBodyVisible();
    }

    /**
     * Get panel class.
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return false;
    }

    /**
     * Return file name for body template.
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'model/table.twig';
    }

    /**
     * Check whether the pager box is visible.
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return false;
    }

    /**
     * Get preprocessed columns structure.
     *
     * @return array
     */
    protected function getColumns()
    {
        if (!isset($this->columns)) {
            foreach ($this->defineColumns() as $idx => $column) {
                $column[static::COLUMN_CODE] = $idx;
                $column[static::COLUMN_METHOD_SUFFIX] = \Includes\Utils\Converter::convertToUpperCamelCase($column[static::COLUMN_CODE]);
                if (!isset($column[static::COLUMN_TEMPLATE]) && !isset($column[static::COLUMN_CLASS])) {
                    $column[static::COLUMN_TEMPLATE] = 'items_list/model/table/field.twig';
                }
                $column[static::COLUMN_PARAMS] = $column[static::COLUMN_PARAMS] ?? [];
                $this->columns[] = $column;
            }
        }

        return $this->columns;
    }

    /**
     * Returns columns count.
     *
     * @return integer
     */
    protected function getColumnsCount()
    {
        return count($this->getColumns());
    }

    /**
     * Get column value.
     *
     * @param array $column Column
     * @param mixed $row    Model
     *
     * @return mixed
     */
    protected function getColumnValue(array $column, $row)
    {
        $suffix = $column[static::COLUMN_METHOD_SUFFIX];

        if (is_object($row)) {
            // Getter
            $method = 'get' . $suffix . 'ColumnValue';
            $value = method_exists($this, $method)
                // Call the getter method if exists
                ? $this->$method($row)
                // ... otherwise read from the property
                : $row->{$column[static::COLUMN_CODE]};
        } elseif (is_array($row)) {
            $value = $row[$column[static::COLUMN_CODE]] ?? null;
        } else {
            $value = $row;
        }

        // Preprocessing
        $method = 'preprocess' . \Includes\Utils\Converter::convertToUpperCamelCase($column[static::COLUMN_CODE]);
        if (method_exists($this, $method)) {
            // $method assembled frm 'preprocess' + field name
            $value = $this->$method($value, $column, $row);
        }

        return $value;
    }

    /**
     * Check - table header is visible or not.
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
     * Return dir which contains the page body template.
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'model/table';
    }

    /**
     * Check if the column template is used for widget displaying.
     *
     * @param array $column Column information
     * @param mixed $row    Row data
     *
     * @return boolean
     */
    protected function isTemplateColumnVisible(array $column, $row)
    {
        return !empty($column[static::COLUMN_TEMPLATE]);
    }

    /**
     * Define CSS classes for the row.
     *
     * @param integer $index Line index
     * @param mixed   $row   Line
     *
     * @return array
     */
    protected function defineLineClass($index, $row)
    {
        $classes = ['line'];

        if ($index === 0) {
            $classes[] = 'first';
        }

        if ($this->getItemsCount() == $index + 1) {
            $classes[] = 'last';
        }

        if (0 === ($index + 1) % $this->highlightStep) {
            $classes[] = 'even';
        }

        return $classes;
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
     * Return class name for the list pager.
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }

    /**
     * Return internal list name.
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.' . implode('.', $this->getListNameSuffixes());
    }

    /**
     * Get list name suffixes.
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        $parts = explode('\\', get_called_class());

        $names = [];
        if ($parts[1] === 'Module') {
            $names[] = strtolower($parts[2]);
            $names[] = strtolower($parts[3]);
        }

        $names[] = strtolower($parts[count($parts) - 1]);

        return $names;
    }

    /**
     * Get container class.
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'widget items-list'
            . ' widgetclass-' . $this->getWidgetClass()
            . ' widgettarget-' . $this->getWidgetTarget()
            . ' sessioncell-' . $this->getSessionCell()
            . ' items-list-table'
            . ($this->isTableHeaderVisible() ? ' no-thead' : '');
    }

    /**
     * Get head class.
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
     *
     * @return bool|mixed
     */
    protected function isNoColumnHead(array $column)
    {
        return false;
    }

    /**
     * @param array $column
     *
     * @return int|mixed
     */
    protected function getColumnHeadColspan(array $column)
    {
        return 1;
    }

    /**
     * Check if at least one of headers has subheader
     *
     * @return boolean
     */
    protected function hasSubheaders()
    {
        return false;
    }

    /**
     * Check - search-in-head mechanism is available or not.
     *
     * @return boolean
     */
    protected function isHeadSearchVisible()
    {
        return false;
    }

    /**
     * List has top creation box.
     *
     * @return boolean
     */
    protected function isTopInlineCreation()
    {
        return false;
    }

    /**
     * Get column cell class.
     *
     * @param array $column Column
     * @param mixed $row    Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, $row = null)
    {
        return 'cell '
            . $column[static::COLUMN_CODE]
            . (empty($column[static::COLUMN_NO_WRAP]) ? '' : ' no-wrap');
    }

    /**
     * Check if the simple class is used for widget displaying.
     *
     * @param array $column Column information
     * @param mixed $row    Row data
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, $row)
    {
        return !isset($column[static::COLUMN_TEMPLATE]);
    }

    /**
     * List has bottom creation box.
     *
     * @return boolean
     */
    protected function isBottomInlineCreation()
    {
        return false;
    }

    /**
     * Get container attributes.
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        return [
            'class' => $this->getContainerClass(),
        ];
    }

    /**
     * Get container attributes as string.
     *
     * @return string
     */
    protected function getContainerAttributesAsString()
    {
        $list = [];
        foreach ($this->getContainerAttributes() as $name => $value) {
            $list[] = $name . '="' . func_htmlspecialchars($value) . '"';
        }

        return implode(' ', $list);
    }

    /**
     * Get cell list name part.
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

    /**
     * Return number of rows in the list.
     *
     * @return array
     */
    protected function getItemsCount()
    {
        if (!isset($this->rawPageData)) {
            $this->initRawPageData();
            $this->itemsCount = count($this->rawPageData);
        }

        return $this->itemsCount;
    }

    /**
     * Return data for the model table.
     *
     * @return array
     */
    protected function getPageData()
    {
        if (!isset($this->rawPageData)) {
            $this->initRawPageData();
        }

        return $this->rawPageData;
    }

    /**
     * Get line attributes
     *
     * @param integer $index Line index
     * @param array   $row   Line data OPTIONAL
     *
     * @return array
     */
    protected function getLineAttributes($index, $row = null)
    {
        $result = [
            'class'   => $this->defineLineClass($index, $row),
        ];

        if ($index == -1) {
            $result['style'] = 'display: none;';
        }

        return $result;
    }

    /**
     * Return true if 'Edit' link should be displayed in column line
     *
     * @param array $column Column information
     * @param array $row    Row data
     *
     * @return boolean
     */
    protected function isEditLinkEnabled(array $column, $row)
    {
        return false;
    }

    /**
     * Return true if items list should be displayed in static mode (no editable widgets, no controls)
     *
     * @return boolean
     */
    protected function isStatic()
    {
        return true;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function isWrapWithForm()
    {
        return false;
    }

    /**
     * Get self
     *
     * @return \XLite\View\ItemsList\Model\AModel
     */
    protected function getSelf()
    {
        return $this;
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
    protected function isLink(array $column, $entity)
    {
        return isset($column[static::COLUMN_LINK]);
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
}
