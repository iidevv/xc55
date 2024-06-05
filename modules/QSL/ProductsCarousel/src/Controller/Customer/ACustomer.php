<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract customer
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * @return mixed
     */
    protected function getProductsCarouselConfig()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductsCarousel;
    }

    /**
     * @return bool
     */
    protected function isShowProductsCarouselNavigation()
    {
        return $this->getProductsCarouselConfig()->navigation == 'buttonsOnly'
            || $this->getProductsCarouselConfig()->navigation == 'paginationAndButtons';
    }

    /**
     * @return bool
     */
    protected function isShowProductsCarouselPagination()
    {
        return $this->getProductsCarouselConfig()->navigation == 'paginationOnly'
            || $this->getProductsCarouselConfig()->navigation == 'paginationAndButtons';
    }

    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        $list = parent::defineCommonJSData();

        $responsive_array = [
            'carousel_nav'        => $this->isShowProductsCarouselNavigation(),
            'carousel_pagination' => $this->isShowProductsCarouselPagination(),

            'carousel_responsive_one_column'    => [
                0    => [
                    "items" => 2,
                ],
                480  => [
                    "items" => 2,
                ],
                768  => [
                    "items" => 2,
                ],
                992  => [
                    "items" => 3,
                ],
                1200 => [
                    "items" => 4,
                ],
            ],
            'carousel_responsive_two_columns'   => [
                0    => [
                    "items" => 2,
                ],
                480  => [
                    "items" => 2,
                ],
                768  => [
                    "items" => 2,
                ],
                992  => [
                    "items" => 3,
                ],
                1200 => [
                    "items" => 3,
                ],
            ],
            'carousel_responsive_three_columns' => [
                0    => [
                    "items" => 2,
                ],
                480  => [
                    "items" => 2,
                ],
                768  => [
                    "items" => 2,
                ],
                992  => [
                    "items" => 2,
                ],
                1200 => [
                    "items" => 2,
                ],
            ],
        ];

        return array_merge(
            $list,
            $responsive_array
        );
    }
}
