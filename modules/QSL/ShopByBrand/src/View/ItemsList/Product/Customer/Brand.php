<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\ListChild;
use XLite\View\CacheableTrait;

/**
 * List of brand products.
 *
 * @ListChild (list="center.bottom", zone="customer", weight="200")
 */
class Brand extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use CacheableTrait;
    use \XLite\View\ItemsList\Product\Customer\DefaultSortByTrait;

    /**
     * Widget parameter names
     */
    public const PARAM_BRAND_ID = 'brand_id';

    /**
     * Widget target
     */
    public const WIDGET_TARGET = 'brand';

    /**
     * Allowed sort criterions
     */
    public const SORT_BY_MODE_DEFAULT = 'bp.orderby';

    /**
     * Brand
     *
     * @var \QSL\ShopByBrand\Model\Brand
     */
    protected $brand;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'brand';

        return $result;
    }

    /**
     * Return target to retrive this widget from AJAX.
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET;
    }

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->sortByModes = [
            static::SORT_BY_MODE_DEFAULT => 'Recommended'
        ] + $this->sortByModes;
    }

    /**
     * Get products single order 'sort by' fields
     * Return in format [sort_by_field => sort_order]
     *
     * @return array
     */
    protected function getSingleOrderSortByFields()
    {
        return parent::getSingleOrderSortByFields() + [
            static::SORT_BY_MODE_DEFAULT => static::SORT_ORDER_ASC
        ];
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' brand-products';
    }

    /**
     * Get default sort order value
     *
     * @return string
     */
    protected function getDefaultSortOrderValue()
    {
        return \XLite\Core\Config::getInstance()->General->default_brand_products_order;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'QSL\ShopByBrand\View\Pager\Customer\Product\Brand';
    }

    /**
     * Get requested brand object
     *
     * @return \XLite\Model\Category
     */
    protected function getBrand()
    {
        if (!isset($this->brand)) {
            $id = $this->getBrandId();
            if ($id) {
                $this->brand = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')
                    ->find($id);
            }
        }

        return $this->brand;
    }

    /**
     * Get requested brand ID
     *
     * @return int
     */
    protected function getBrandId()
    {
        return intval(\XLite\Core\Request::getInstance()->{static::PARAM_BRAND_ID});
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_BRAND_ID => new \QSL\ShopByBrand\Model\WidgetParam\ObjectId\Brand('Brand ID', null),
        ];
    }

    /**
     * Define so called "request" parameters.
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_BRAND_ID;
    }

    /**
     * Return products list.
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param bool                   $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|void
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $brand = $this->getBrand();

        return $brand ? $brand->getProducts($cnd, $countOnly) : null;
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list             = parent::getWidgetParameters();
        $list['brand_id'] = \XLite\Core\Request::getInstance()->brand_id;

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return bool
     */
    protected function isVisible()
    {
        return !!$this->getBrand();
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getListHead()
    {
        return static::t('No products available');
    }

    /**
     * Check if head title is visible
     *
     * @return bool
     */
    protected function isHeadVisible()
    {
        return $this->getItemsCount() === 0;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = $this->getBrandId();
        $list[] = \XLite\Core\Config::getInstance()->QSL->ShopByBrand->shop_by_brand_pager;

        return $list;
    }

    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DEFAULT;
    }

    /**
     * Defines if the widget is listening to get params
     *
     * @return bool
     */
    protected function isListenToGetParams()
    {
        return true;
    }
}
