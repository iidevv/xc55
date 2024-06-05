<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrdersImport\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Order repository
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    /**
     * Next order number is initialized with the maximum order number
     *
     * @param integer $number force increase number counter OPTIONAL
     *
     * @return void
     */
    public function initializeNextOrderNumber(int $number = 0)
    {
        $max = $this->getMaxOrderNumber();
        $value = $number && $number > $max ? $number : $max;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            [
                'category'  => 'General',
                'name'      => 'order_number_counter',
                'value'     => $value + 1,
            ]
        );
    }
}
