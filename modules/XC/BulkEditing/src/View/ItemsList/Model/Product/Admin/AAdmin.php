<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\View\ItemsList\Model\Product\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\ItemsList\Model\Product\Admin\AAdmin
{
    /**
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(parent::getFormParams(), ['scenario' => '']);
    }
}
