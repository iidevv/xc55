<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\View\ItemsList\Model\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\View\ItemsList\Model\Customer\Review
{

    protected function isAdditionalFieldVisible($name)
    {
        return Config::getInstance()->XC->Reviews->{$name};
    }

    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $request = Request::getInstance();

        if ($request->order_useful) {
            $cnd->order_useful = true;
        }

        if ($request->order_date) {
            $cnd->order_date = true;
        }

        return parent::getData($cnd, $countOnly);
    }


}