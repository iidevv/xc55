<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View;

use XCart\Extender\Mapping\Extender;

/**
 * Product attribute values
 * @Extender\Mixin
 */
class ChangeAttributeValues extends \XLite\View\ChangeAttributeValues
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/QSL/ColorSwatches/product/attribute_value/select/controller.js';

        return $list;
    }
}
