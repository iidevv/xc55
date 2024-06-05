<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->sortByModes += [
            static::SORT_BY_MODE_BOUGHT => 'Sales sort',
        ];
    }

    /**
     * Get products 'sort by' fields
     *
     * @return array
     */
    protected function getSortByFields()
    {
        $fields = parent::getSortByFields();
        $fields += [
            'bought' => static::SORT_BY_MODE_BOUGHT,
        ];

        return $fields;
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
            static::SORT_BY_MODE_BOUGHT => static::SORT_ORDER_DESC,
        ];
    }
}
