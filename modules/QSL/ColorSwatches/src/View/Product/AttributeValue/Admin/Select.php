<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\Product\AttributeValue\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Attribute value (Select)
 * @Extender\Mixin
 */
class Select extends \XLite\View\Product\AttributeValue\Admin\Select
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ColorSwatches/product/manage_attribute_value/select/body.twig';
    }
}
