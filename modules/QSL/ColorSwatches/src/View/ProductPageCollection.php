<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View;

use XCart\Extender\Mapping\Extender;

/**
 * Product page widgets collection
 * @Extender\Mixin
 */
class ProductPageCollection extends \XLite\View\ProductPageCollection
{
    /**
     * @inherit
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        return array_merge(
            parent::defineWidgetsCollection(),
            [
                '\XLite\View\Product\Details\Customer\EditableAttributes',
            ]
        );
    }

    /**
     * Check - allowed display subwidget or not
     *
     * @param string $name Widget class name
     *
     * @return boolean
     */
    protected function isAllowedWidget($name)
    {
        switch ($name) {
            case '\XLite\View\Product\Details\Customer\EditableAttributes':
                return true;
        }

        return parent::isAllowedWidget($name);
    }
}
