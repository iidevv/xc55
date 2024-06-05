<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\Form\Product\Modify;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Single extends \XLite\View\Form\Product\Modify\Single
{
    /**
     * Set validators pairs for products data
     *
     * @param mixed &$data Data
     *
     * @return void
     */
    protected function setDataValidators(&$data)
    {
        parent::setDataValidators($data);

        $this->setSaleDataValidators($data);
    }
}
