<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\ProductsCarousel\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract customer
 * @Extender\Mixin
 * @Extender\After ("QSL\ProductsCarousel")
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
{

    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        $list = parent::defineCommonJSData();

        $responsive_array = [];

        $list['carousel_responsive_one_column'] = [
            0 => [
                "autoWidth" => true,
                "margin" => 16,
                "nav" => false,
                "dots" => true
            ],
            480 => [
                "items" => 3,
                "margin" => 12,
            ],
            768 => [
                "items" => 4,
                "margin" => 12,
            ],
            992 => [
                "items" => 4,
                "margin" => 16,
            ],
            1200 => [
                "items" => 5,
                "margin" => 16,
            ],
            1352 => [
                "items" => 6,
                "margin" => 16,
            ],
        ];

        $list['carousel_responsive_two_columns'] = [
            0 => [
                "autoWidth" => true,
                "margin" => 16,
                "nav" => false,
                "dots" => true
            ],
            480 => [
                "items" => 3,
                "margin" => 16
            ],
            768 => [
                "items" => 4,
                "margin" => 16
            ],
            992 => [
                "items" => 3,
                "margin" => 20
            ],
            1200 => [
                "items" => 4,
            ],
            1352 => [
                "items" => 5,
                "margin" => 16,
            ],
        ];

        $list['carousel_responsive_three_columns'] = [
            0 => [
                "autoWidth" => true,
                "margin" => 16,
                "nav" => false,
                "dots" => true
            ],
            480 => [
                "items" => 3,
                "margin" => 16
            ],
            768 => [
                "items" => 4,
                "margin" => 16
            ],
            992  => [
                "items" => 4,
                "margin" => 20
            ],
            1200 => [
                "items" => 4,
            ],
        ];

        $list['carousel_responsive_wide'] = [
            0 => [
                "autoWidth" => true,
                "margin" => 16,
                "nav" => false,
                "dots" => true
            ],
            480 => [
                "items" => 3,
                "margin" => 16
            ],
            768 => [
                "items" => 4,
                "margin" => 16
            ],
            992 => [
                "items" => 4,
                "margin" => 16,
            ],
            1200 => [
                "items" => 5,
                "margin" => 16
            ],
            1200 => [
                "items" => 6,
                "margin" => 16
            ],
            1352 => [
                "items" => 8,
                "margin" => 16,
            ],
        ];

        return array_merge(
            $list,
            $responsive_array
        );
    }
}
