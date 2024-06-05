<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\MultiVendor;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\AuthService;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\MultiVendor")
 *
 */

class XCartContext extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext
{
    public function hasCustomerAccess()
    {
        return parent::hasCustomerAccess() && !$this->hasVendorAccess();
    }

    /**
     * @return boolean
     */
    public function hasVendorAccess()
    {
        $token = $this->getAuthToken();

        return $token['access'] === AuthService::ACCESS_VENDOR;
    }
}
