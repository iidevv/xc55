<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View;

use XCart\Extender\Mapping\Extender;

/**
 * Product page widgets collection
 * @Extender\Mixin
 */
class ProductPageCollection extends \XLite\View\ProductPageCollection
{
    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        return array_merge(
            parent::defineWidgetsCollection(),
            [
                '\QSL\LoyaltyProgram\View\Product\Details\ProductPoints',
            ]
        );
    }
}
