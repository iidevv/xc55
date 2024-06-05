<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Logic\ImageResize\Step;

/**
 * Product variants
 */
class ProductVariants extends \XLite\Logic\ImageResize\Step\AStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XC\ProductVariants\Model\Image\ProductVariant\Image');
    }

    // }}}
}
