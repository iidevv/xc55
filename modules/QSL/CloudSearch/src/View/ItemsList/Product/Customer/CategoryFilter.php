<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\View\ItemsList\Product\Customer;

use QSL\CloudSearch\Main;
use QSL\CloudSearch\Model\Repo\Product;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;

/**
 * Search products item list
 *
 * @Extender\Mixin
 */
class CategoryFilter extends \XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter
{
    use FilterWithCloudSearchTrait;

    const PARAM_CLOUD_FILTERS = 'cloudFilters';

    const PARAM_LOAD_PRODUCTS_WITH_CLOUD_SEARCH = 'loadProductsWithCloudSearch';

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->initializeCsLimitCondition();

        parent::__construct($params);
    }

    /**
     * @return string
     */
    protected function getEmptyFilteredListTemplate()
    {
        return 'modules/QSL/CloudSearch/cloud_filters/empty_filtered_product_list.twig';
    }

    /**
     * Check if product list should be loaded with CloudSearch
     *
     * @param CommonCell $cnd
     * @return bool
     */
    protected function isLoadingWithCloudSearch(CommonCell $cnd)
    {
        return Main::isConfigured()
               && Main::isCloudFiltersEnabled()
               && !empty($cnd->{Product::P_CATEGORY_ID})
               && !empty($cnd->{Product::P_CLOUD_FILTERS});
    }

    /**
     * Check if product list should have a Filter section
     *
     * @param CommonCell $cnd
     *
     * @return bool
     */
    protected function isFilteringWithCloudSearch(CommonCell $cnd)
    {
        return Main::isCloudFiltersEnabled()
               && !empty($cnd->{Product::P_CATEGORY_ID});
    }

    /**
     * Check if Filter section should be loaded asynchronously on the client side
     *
     * @param CommonCell $cnd
     *
     * @return bool
     */
    protected function isAsynchronouslyFilteringWithCloudSearch(CommonCell $cnd)
    {
        return empty($cnd->{Product::P_CLOUD_FILTERS});
    }
}
