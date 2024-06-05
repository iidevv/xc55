<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\View\ItemsList\Product\Customer;

use Includes\Utils\Module\Manager;
use QSL\CloudSearch\Main;
use QSL\CloudSearch\Model\Repo\Product;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;

/**
 * Search products item list
 *
 * @Extender\Mixin
 */
abstract class Search extends \XLite\View\ItemsList\Product\Customer\Search
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

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CloudSearch/search_style.css';

        return $list;
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
     * @return string
     */
    protected function getEmptyFilteredListTemplate()
    {
        return 'modules/QSL/CloudSearch/cloud_filters/empty_filtered_search.twig';
    }

    /**
     * Check if product list should be loaded with CloudSearch
     *
     * @param CommonCell $cnd
     *
     * @return bool
     */
    protected function isLoadingWithCloudSearch(CommonCell $cnd)
    {
        $result = false;

        if (Main::isConfigured()) {
            // Always set value to true for Make/Model/Year filter search results
            $result = Manager::getRegistry()->isModuleEnabled('QSL', 'Make')
                && \QSL\Make\Main::hasActiveLevelFilterId();

            if (!$result) {
                $result = ($cnd->{Product::P_SUBSTRING} !== '' && $cnd->{Product::P_SUBSTRING} !== null)
                    || (Main::isCloudFiltersEnabled() && !empty($cnd->{Product::P_CLOUD_FILTERS}));
            }
        }

        return $result;
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
        return Main::isCloudFiltersEnabled();
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
        return false;
    }
}
