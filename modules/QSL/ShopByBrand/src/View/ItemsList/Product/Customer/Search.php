<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Search Products list.
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Product\Customer\Search
{
    /**
     * Widget param names
     */
    public const PARAM_BRAND_ID = 'brandId';

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        $params = parent::getSearchParams();

        $params[\XLite\Model\Repo\Product::P_BRAND_ID] = self::PARAM_BRAND_ID;

        return $params;
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_BRAND_ID => new \XLite\Model\WidgetParam\TypeInt(
                'Brand ID',
                0
            ),
        ];
    }
}
