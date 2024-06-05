<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * @return mixed
     */
    public static function isCustomerFollowupEnabled()
    {
        return (bool)\XLite\Core\Config::getInstance()->XC->Reviews->enableCustomersFollowup;
    }
}
