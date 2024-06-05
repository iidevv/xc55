<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_DATE  = 'p.arrivalDate';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->sortByModes = [
            static::SORT_BY_MODE_DATE => 'Newest first',
        ] + $this->sortByModes;
    }

    /**
     * Get products 'sort by' fields
     *
     * @return array
     */
    protected function getSortByFields()
    {
        return [
            'newest' => static::SORT_BY_MODE_DATE,
        ] + parent::getSortByFields();
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/ProductAdvisor/style.css';

        return $list;
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
            static::SORT_BY_MODE_DATE => static::SORT_ORDER_DESC
        ];
    }
}
