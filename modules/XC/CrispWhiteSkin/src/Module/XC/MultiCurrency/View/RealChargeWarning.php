<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\MultiCurrency\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiCurrency")
 */
class RealChargeWarning extends \XC\MultiCurrency\View\RealChargeWarning
{
    /**
     * @return string[]
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'file'  => 'modules/XC/MultiCurrency/css/style.less',
                'merge' => 'bootstrap/css/bootstrap.less',
            ]
        );
    }
}
