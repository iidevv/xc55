<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Module\XC\MultiVendor\View\VendorInfo;

use XCart\Extender\Mapping\Extender;

/**
 * Class Product
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class VendorPage extends \XC\MultiVendor\View\VendorInfo\VendorPage
{
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/XC/VendorMessages/vendor_info/style.less'
            ]
        );
    }
}
