<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ProductPageCollection extends \XLite\View\ProductPageCollection
{
    /**
     * @inheritDoc
     */
    protected function defineWidgetsCollection()
    {
        $widgets = parent::defineWidgetsCollection();

        $widgets = array_merge(
            $widgets,
            [
                '\QSL\BackInStock\View\Product\Details\Customer\CustomerNote',
                '\QSL\BackInStock\View\Product\Details\Customer\NotifyMe',
            ]
        );

        return array_unique($widgets);
    }
}
