<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\ItemsList\Brand\Customer;

use XCart\Extender\Mapping\ListChild;
use XLite\View\CacheableTrait;
use QSL\ShopByBrand\Controller\Customer\Brands as BrandsController;

/**
 * List of brands in the storefront.
 *
 * @ListChild (list="center", zone="customer")
 */
class Brand extends \XLite\View\ItemsList\AItemsList
{
    use CacheableTrait;

    /**
     * Widget param names
     */
    public const PARAM_ICON_MAX_WIDTH  = 'iconWidth';
    public const PARAM_ICON_MAX_HEIGHT = 'iconHeight';
    public const PARAM_GRID_COLUMNS    = 'gridColumns';

    /**
     * Columns number range
     */
    public const GRID_COLUMNS_MIN = 1;
    public const GRID_COLUMNS_MAX = 5;

    /**
     * Widget target
     */
    public const WIDGET_TARGET = 'brands';

    /**
     * Rows of brands.
     *
     * @var array
     */
    protected $rows;

    /**
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            \QSL\ShopByBrand\Model\Repo\Brand::SORT_BY_PRODUCT_COUNT => 'Number of products',
            \QSL\ShopByBrand\Model\Repo\Brand::SORT_BY_BRAND_NAME    => 'Brand name',
            \QSL\ShopByBrand\Model\Repo\Brand::SORT_BY_ADMIN_DEFINED => 'Default',
        ];

        parent::__construct($params);
    }

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = static::getWidgetTarget();

        return $result;
    }

    /**
     * @return array
     */
    public static function getIconSizes()
    {
        $model = \XLite\Logic\ImageResize\Generator::MODEL_BRAND;

        return [
            'grid' => \XLite\Logic\ImageResize\Generator::getImageSizes($model, 'Default'),
        ];
    }

    /**
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = self::getDir() . '/brands_list_styles.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/brands_list.js';

        return $list;
    }

    /**
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' items-list-brands';
    }

    /**
     * @param array $params Widget params
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if (
            !isset($params[static::PARAM_ICON_MAX_WIDTH])
            && !isset($params[static::PARAM_ICON_MAX_HEIGHT])
            && $this->getParam(static::PARAM_ICON_MAX_WIDTH) == 0
            && $this->getParam(static::PARAM_ICON_MAX_HEIGHT) == 0
        ) {
            // TODO: switch to the size parameters logic
            $sizes = static::getIconSizes();
            $size  = $sizes['grid'];
            $this->widgetParams[static::PARAM_ICON_MAX_WIDTH]->setValue($size[0]);
            $this->widgetParams[static::PARAM_ICON_MAX_HEIGHT]->setValue($size[1]);
        }

        $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]->setValue(
            \XLite\Core\Config::getInstance()->QSL->ShopByBrand->shop_by_brand_pager
        );
    }

    /**
     * Get brands split into rows.
     *
     * @return array
     */
    public function getBrandRows()
    {
        if (!isset($this->rows)) {
            $this->rows = $this->defineBrandRows();
        }

        return $this->rows;
    }

    /**
     * Get the number of grid columns to display brands.
     *
     * @return int
     */
    public function getColumnsCount()
    {
        return $this->getParam(static::PARAM_GRID_COLUMNS);
    }

    /**
     * Count the total number of rows.
     *
     * @return int
     */
    public function countRows()
    {
        return isset($this->rows) ? count($this->rows) : count($this->getBrandRows());
    }

    /**
     * Brand logo width.
     * DEPRECTATED. Use getIconWidth() instead.
     *
     * @return int
     */
    public function getLogoWidth()
    {
        return $this->getIconWidth();
    }

    /**
     * Brand logo height.
     * DEPRECTATED. Use getIconHeight() instead.
     *
     * @return int
     */
    public function getLogoHeight()
    {
        return $this->getIconHeight();
    }

    /**
     * Get CSS class for the row tag.
     *
     * @param int $row Row index.
     *
     * @return string
     */
    public function getRowCSSClass($row)
    {
        if (!$row) {
            $class = 'first';
        } elseif ($row == $this->countRows() - 1) {
            $class = 'last';
        } else {
            $class = '';
        }

        return $class;
    }

    /**
     * Get CSS class for the row tag.
     *
     * @param int $row    Row index.
     * @param int $column Column index.
     *
     * @return string
     */
    public function getColumnCSSClass($row, $column)
    {
        if (!$column) {
            $class = 'first';
        } elseif ($column == $this->getColumnsCount() - 1) {
            $class = 'last';
        } else {
            $class = '';
        }

        return $class;
    }

    /**
     * Returns the inline CSS for an item in the grid of brands.
     *
     * @return string
     */
    public function getItemInlineStyle()
    {
        $items = [];

        $min = $this->getMinItemWidth();
        if ($min) {
            $items[] = "min-width: {$min}";
        }

        $max = $this->getMaxItemWidth();
        if ($max) {
            $items[] = "max-width: {$max}";
        }

        return implode('; ', $items);
    }

    /**
     * Return the minimum width of an item in the grid.
     *
     * @return string
     */
    public function getMinItemWidth()
    {
        return ($this->getIconWidth() + 70) . 'px';
    }

    /**
     * Return the minimum width of an item in the grid.
     *
     * @return string
     */
    public function getMaxItemWidth()
    {
        return (($this->getIconWidth() + 70) * 2) . 'px';
    }

    /**
     * @return array
     */
    protected function getSortByFields()
    {
        return [
            'products' => \QSL\ShopByBrand\Model\Repo\Brand::SORT_BY_PRODUCT_COUNT,
            'brand'    => \QSL\ShopByBrand\Model\Repo\Brand::SORT_BY_BRAND_NAME,
            'default'  => \QSL\ShopByBrand\Model\Repo\Brand::SORT_BY_ADMIN_DEFINED,
        ];
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/ShopByBrand/items_list/brand';
    }

    /**
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'page';
    }

    /**
     * @return bool
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getEmptyListDir()
    {
        return "{$this->getDir()}/{$this->getPageBodyDir()}";
    }

    /**
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.brand.customer';
    }

    /**
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return \XLite\Core\Config::getInstance()->QSL->ShopByBrand->shop_by_brand_page_order;
    }

    /**
     * @return string
     */
    protected function getSortBy()
    {
        return $this->getSortByModeDefault();
    }

    /**
     * @return string
     */
    protected function getSortOrder()
    {
        return ($this->getSortBy() === \QSL\ShopByBrand\Model\Repo\Brand::SORT_BY_PRODUCT_COUNT)
            ? static::SORT_ORDER_DESC
            : static::SORT_ORDER_ASC;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{\QSL\ShopByBrand\Model\Repo\Brand::SEARCH_ORDER_BY} = $this->getOrderBy();

        return $result;
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_GRID_COLUMNS    => new \XLite\Model\WidgetParam\TypeSet(
                'Number of columns (for Grid mode only)',
                3,
                true,
                $this->getGridColumnsRange()
            ),
            static::PARAM_ICON_MAX_WIDTH  => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon width',
                0,
                true
            ),
            static::PARAM_ICON_MAX_HEIGHT => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon height',
                0,
                true
            ),
        ];
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        return \XLite\Core\Config::getInstance()->QSL->ShopByBrand->shop_by_brand_pager
            ? parent::getLimitCondition()
            : $this->getSearchCondition();
    }

    /**
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * @return bool
     */
    protected function isPagerVisible()
    {
        return parent::isPagerVisible()
            && $this->getItemsCount() > 0
            && $this->getParam(\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR);
    }

    /**
     * Return products split into rows.
     *
     * @return array
     */
    protected function defineBrandRows()
    {
        $data = $this->getPageData();
        $rows = [];

        if (!empty($data)) {
            $count       = $this->getColumnsCount();
            $rows        = array_chunk($data, $count);
            $last        = count($rows) - 1;
            $rows[$last] = array_pad($rows[$last], $count, false);
        }

        return $rows;
    }

    /**
     * @return array
     */
    protected function getGridColumnsRange()
    {
        return range(static::GRID_COLUMNS_MIN, static::GRID_COLUMNS_MAX);
    }

    /**
     * @return int
     */
    protected function getGridItemWidth()
    {
        return floor(100 / $this->getParam(static::PARAM_GRID_COLUMNS)) - 6;
    }

    /**
     * @return int
     */
    protected function getIconWidth()
    {
        return $this->getParam(static::PARAM_ICON_MAX_WIDTH);
    }

    /**
     * @return int
     */
    protected function getIconHeight()
    {
        return $this->getParam(static::PARAM_ICON_MAX_HEIGHT);
    }

    /**
     * @param \QSL\ShopByBrand\Model\Brand $brand Current brand
     *
     * @return string
     */
    protected function getIconAlt($brand)
    {
        return $brand->getImage() && $brand->getImage()->getAlt()
            ? $brand->getImage()->getAlt()
            : $brand->getName();
    }

    /**
     * @return int
     */
    protected function getTableColumnsCount()
    {
        return 3;
    }

    /**
     * @return array
     */
    protected function getCacheParameters()
    {
        $list   = parent::getCacheParameters();
        $list[] = \XLite\Core\Config::getInstance()->General->show_out_of_stock_products;

        foreach ($this->defineCachedParams() as $name) {
            $list[] = $this->getRequestParamValue($name)
                ?: (($widgetParam = $this->getWidgetParams($name)) ? $widgetParam->value : '');
        }

        return $list;
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        return array_merge(
            parent::getWidgetParameters(),
            [
                BrandsController::PARAM_FIRST_LETTER => $this->getFirstLetter(),
                BrandsController::PARAM_SUBSTRING    => $this->getSubstring(),
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineCachedParams()
    {
        return [
            BrandsController::PARAM_FIRST_LETTER,
            BrandsController::PARAM_SUBSTRING,
            \XLite\View\Pager\APager::PARAM_PAGE_ID,
            \XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE,
            static::PARAM_SORT_BY,
            static::PARAM_SORT_ORDER,
        ];
    }

    /**
     * @return string
     */
    protected function getPagerClass()
    {
        return 'QSL\ShopByBrand\View\Pager\Customer\Brand';
    }

    /**
     * Return brand list.
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param bool                   $countOnly Return items list or only its size OPTIONAL
     *
     * @return array
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        /** @var \QSL\ShopByBrand\Model\Repo\Brand $repo */
        $repo = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand');

        if ($this->getFirstLetter() !== null) {
            $cnd->{$repo::SEARCH_STARTS_WITH} = $this->getFirstLetter();
        } elseif ($this->getSubstring() !== null) {
            $cnd->{$repo::SEARCH_SUBSTRING} = $this->getSubstring();
        }

        $result = $countOnly
            ? $repo->countEnabledBrands($cnd)
            : $repo->getCategoryBrandsWithProductCount(
                0,
                $this->isBrandWithoutProductsHidden(),
                0,
                $this->getSortBy(),
                $cnd
            );

        return $result;
    }

    /**
     * @return bool
     */
    protected function isBrandWithoutProductsHidden()
    {
        return (bool) \XLite\Core\Config::getInstance()->QSL->ShopByBrand->hide_brands_without_products;
    }

    /**
     * Check if the list should display number of products per brand.
     *
     * @return bool
     */
    protected function isProductCountVisible()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getWrapperClass()
    {
        return 'all-brands-section all-brands-scheme-'
            . strtolower(\XLite\Core\Layout::getInstance()->getLayoutColor());
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . LC_DS . parent::getBodyTemplate();
    }

    /**
     * @return array
     */
    protected function getURLParams()
    {
        $urlParams = parent::getURLParams();

        if ($this->getFirstLetter() !== null) {
            $urlParams[BrandsController::PARAM_FIRST_LETTER] = $this->getFirstLetter();
        }

        if ($this->getSubstring() !== null) {
            $urlParams[BrandsController::PARAM_SUBSTRING] = $this->getSubstring();
        }

        return $urlParams;
    }

    /**
     * Get session cell name for the certain list items widget
     *
     * @return string
     */
    public static function getSessionCellName()
    {
        return parent::getSessionCellName()
            . '_' . \XLite\Core\Request::getInstance()->{BrandsController::PARAM_FIRST_LETTER}
            . '_' . \XLite\Core\Request::getInstance()->{BrandsController::PARAM_SUBSTRING};
    }
}
