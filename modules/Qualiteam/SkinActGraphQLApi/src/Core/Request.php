<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class Request extends \XLite\Core\Request
{
    /**
     * Returns token to find cart in API operations
     *
     * @return string
     */
    public function getApiCartToken()
    {
        return \XLite\Core\Request::getInstance()->token
            ?: \XLite\Core\Request::getInstance()->_token;
    }

    /**
     * @return string
     */
    public function getApiCartMode()
    {
        return \XLite\Core\Request::getInstance()->mode ?: 'cart';
    }
}