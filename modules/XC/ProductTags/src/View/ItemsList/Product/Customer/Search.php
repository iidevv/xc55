<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Search
 *
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Product\Customer\Search
{
    /**
     * Widget param names
     */
    public const PARAM_BY_TAG = 'by_tag';

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        $list = parent::getSearchParams();

        $list += [
            \XLite\Model\Repo\Product::P_BY_TAG      => self::PARAM_BY_TAG,
        ];

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_BY_TAG => new \XLite\Model\WidgetParam\TypeString(
                'Search in tags',
                0
            ),
        ];
    }
}
