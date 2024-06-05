<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Order;
use XLite\Model\Product;
use XLite\Model\Profile;

/**
 * @Extender\Mixin
 */
class Autocomplete extends \XLite\Controller\Admin\Autocomplete
{
    /**
     * @param $term
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assembleDictionaryThemeTweakerProfile($term)
    {
        $data = array_map(
            static function (Profile $profile) {
                return $profile->getLogin();
            },
            \XLite\Core\Database::getRepo('\XLite\Model\Profile')
                ->findProfilesByTerm($term, 5)
        );

        return array_combine($data, $data);
    }

    /**
     * @param $term
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assembleDictionaryThemeTweakerProduct($term)
    {
        $data = array_map(
            static function (Product $product) {
                return $product->getSku();
            },
            \XLite\Core\Database::getRepo('\XLite\Model\Product')
                ->findProductsByTerm($term, 5)
        );

        return array_combine($data, $data);
    }

    /**
     * @param $term
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assembleDictionaryThemeTweakerOrder($term)
    {
        $data = array_map(
            static function (Order $order) {
                return $order->getOrderNumber();
            },
            \XLite\Core\Database::getRepo('\XLite\Model\Order')
                ->findOrdersByTerm($term, 5)
        );

        return array_combine($data, $data);
    }
}
