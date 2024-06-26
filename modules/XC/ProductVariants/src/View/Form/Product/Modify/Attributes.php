<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Form\Product\Modify;

/**
 * Attributes
 */
class Attributes extends \XLite\View\Form\Product\Modify\Base\Single
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_variants_attributes';
    }
}
