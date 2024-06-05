<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\MultiVendor\Resolver\System;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\MultiVendor")
 *
 */

class AppConfig extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System\AppConfig
{
    /**
     * @return bool
     */
    protected function isWebviewCheckoutFlow()
    {
        return !\XC\MultiVendor\Main::isWarehouseMode();
    }
}
