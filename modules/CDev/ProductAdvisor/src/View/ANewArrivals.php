<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View;

use XLite\View\CacheableTrait;
use XLite\Core\Config;

/**
 * New arrivals abstract widget class
 *
 */
abstract class ANewArrivals extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use CacheableTrait;

    /**
     * Widget parameter names
     */
    public const PARAM_ROOT_ID     = 'rootId';
    public const PARAM_USE_NODE    = 'useNode';
    public const PARAM_CATEGORY_ID = 'category_id';

    /**
     * Widget target
     */
    public const WIDGET_TARGET_NEW_ARRIVALS = 'new_arrivals';

    /**
     * Return target to retrieve this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return self::WIDGET_TARGET_NEW_ARRIVALS;
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
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' new-arrivals-products';
    }


    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('New arrivals');
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'CDev\ProductAdvisor\View\Pager\Pager';
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        $currentDate = static::getUserTime();
        $daysOffset = abs((int) Config::getInstance()->CDev->ProductAdvisor->na_max_days)
            ?: \CDev\ProductAdvisor\Main::PA_MODULE_OPTION_DEFAULT_DAYS_OFFSET;

        $searchCase->{\CDev\ProductAdvisor\Model\Repo\Product::P_ARRIVAL_DATE} = [
            \XLite\Core\Converter::getDayStart($currentDate - $daysOffset * 24 * 60 * 60),
            \XLite\Core\Converter::getDayEnd($currentDate)
        ];

        return $searchCase;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return Config::getInstance()->CDev->ProductAdvisor->na_enabled
            && parent::isVisible()
            && $this->hasResults();
    }

    /**
     * Set default sort mode by descending of products arrival date
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DATE;
    }

    /**
     * Get max number of products displayed in block
     *
     * @return integer
     */
    protected function getMaxCountInBlock()
    {
        return (int) Config::getInstance()->CDev->ProductAdvisor->na_max_count_in_block ?: 3;
    }

    /**
     * Get max number of products displayed in full list of new arrivals
     *
     * @return integer
     */
    protected function getMaxCountInFullList()
    {
        return (int) Config::getInstance()->CDev->ProductAdvisor->na_max_count_in_full_list;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-new-arrivals';
    }
}
