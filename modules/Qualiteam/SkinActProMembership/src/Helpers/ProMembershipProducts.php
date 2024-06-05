<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Helpers;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Model\Product;

class ProMembershipProducts
{
    use ExecuteCachedTrait;

    public static function getProMembersProductsCount()
    {
        return (new self)->executeCachedRuntime(function () {
            return Database::getRepo(Product::class)
                ->getProMembershipProductsCount();
        }, [
            __METHOD__,
            self::class,
        ]);
    }

    public static function getProMembershipProducts()
    {
        return (new self)->executeCachedRuntime(function () {
            return Database::getRepo(Product::class)
                ->getProMembershipProducts();
        }, [
            __METHOD__,
            self::class,
        ]);
    }

    public static function getProMembershipProduct()
    {
        $product = static::getProMembershipProducts();

        return $product[0] ?? null;
    }
}
