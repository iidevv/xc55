<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Controller\Customer;

use XLite\Core\Request;

/**
 * Class Cart
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * @return bool
     */
    protected function isFormIdValid()
    {
        return parent::isFormIdValid() || $this->checkFormIdForApiCartToken();
    }

    /**
     * Check if form ID needed
     *
     * @return boolean
     */
    protected function checkFormIdForApiCartToken()
    {
        return \XLite\Model\Cart::checkCartExistsForToken(Request::getInstance()->getApiCartToken());
    }
}
