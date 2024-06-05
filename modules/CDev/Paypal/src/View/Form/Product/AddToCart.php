<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Form\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AddToCart extends \XLite\View\Form\Product\AddToCart
{
    /**
     * getFormDefaultParams
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        $list = parent::getFormDefaultParams();

        if (\CDev\Paypal\Main::isExpressCheckoutEnabled()) {
            $list['expressCheckout'] = false;
            $list['inContext'] = true;
            $list['cancelUrl'] = $this->isAjax()
                ? $this->getReferrerURL()
                : \XLite\Core\URLManager::getSelfURI();
        }

        return $list;
    }
}
