<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * PhotoBox
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class PhotoBox extends \XLite\View\Product\Details\Customer\PhotoBox
{
    /**
     * Check - loupe icon is visible or not
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        $result = parent::isLoupeVisible();

        if (!$result && ($product = $this->getProduct()) && $product->hasVariants()) {
            $repo = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\Image\ProductVariant\Image');

            return $repo->countByProduct($product);
        }

        return $result;
    }
}
