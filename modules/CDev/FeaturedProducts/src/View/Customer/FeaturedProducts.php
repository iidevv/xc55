<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View\Customer;

use XCart\Extender\Mapping\ListChild;
use XLite\View\CacheableTrait;

/**
 * Featured products widget
 * @move class to \CDev\FeaturedProducts\View\ItemsList\Product\Customer\Category\
 *
 * @ListChild (list="center.bottom", zone="customer", weight="300")
 */
class FeaturedProducts extends \XLite\View\ItemsList\Product\Customer\Category\ACategory
{
    use CacheableTrait;

    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_DEFAULT = 'featuredProducts.orderBy';

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Product::SEARCH_FEATURED_CATEGORY_ID => static::PARAM_CATEGORY_ID,
        ];
    }

    /**
     * Get default sort order value
     *
     * @return string
     */
    protected function getDefaultSortOrderValue()
    {
        return 'default';
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $this->widgetParams[static::PARAM_DISPLAY_MODE]->setValue($this->getDisplayMode());

        if (isset($this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR])) {
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]->setValue(false);
        }
        if (isset($this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_COUNT])) {
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_COUNT]->setValue(5);
        }
    }

    /**
     * Get widget display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return \XLite\Core\Config::getInstance()->CDev->FeaturedProducts->featured_products_look;
    }

    protected function useWidgetParamsAsSearchDefaults()
    {
        return true;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Featured products');
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_GRID_COLUMNS]->setValue(3);

        unset(
            $this->widgetParams[static::PARAM_SHOW_DISPLAY_MODE_SELECTOR],
            $this->widgetParams[static::PARAM_SHOW_SORT_BY_SELECTOR]
        );
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-featured-products';
    }
}
