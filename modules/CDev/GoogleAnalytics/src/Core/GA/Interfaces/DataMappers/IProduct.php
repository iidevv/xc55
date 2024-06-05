<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers;

use XLite\Model\Product;

interface IProduct extends ICommon
{
    public function getData(Product $product, string $listName = '', string $positionInList = ''): array;

    /**
     * @param Product $product
     * @param string  $listName
     * @param string  $positionInList
     * @param string  $coupon
     * @param null    $qty
     *
     * @return array
     */
    public function getAddProductData(Product $product, string $listName = '', string $positionInList = '', string $coupon = '', $qty = null): array;
}
