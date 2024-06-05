<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\AddToCompare;

use XCart\Extender\Mapping\Extender;

/**
 * Product comparison widget
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductComparison")
 */
class ProductCompareLink extends \XC\ProductComparison\View\AddToCompare\ProductCompareLink
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductComparison/header_settings_link.twig';
    }
}
