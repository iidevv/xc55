<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\CrispWhiteSkin\Module\QSL\PriceCountdown\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\PriceCountdown")
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            ['modules/QSL/PriceCountdown/discount_period_crisp_white.css']
        );
    }
}
