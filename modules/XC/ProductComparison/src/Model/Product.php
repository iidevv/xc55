<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductComparison\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Product
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Check if customer should choose options forced and return needable class string
     *
     * @return string
     */
    protected function getAdditionalClass()
    {
        return \XLite\Core\Config::getInstance()->General->force_choose_product_options !== ''
                && $this->hasEditableAttributes()
                ? 'need-choose-options' : '';
    }
}
