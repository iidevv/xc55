<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\Form\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 *
 * Add product to cart form
 */
class AddToCart extends \XLite\View\Form\Product\AddToCart
{
    /**
     * getFormDefaultParams
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getFormDefaultParams()
    {
        $list = parent::getFormDefaultParams();

        if (Request::getInstance()->ga_list) {
            $list['ga_list'] = Request::getInstance()->ga_list;
        }

        return $list;
    }
}
