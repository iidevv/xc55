<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract product page
 * @Extender\Mixin
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Check - loupe icon is visible or not
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        $result = parent::isLoupeVisible();
        $product = $this->getProduct();

        if (!$result && $product->hasVariants()) {
            $repo = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\Image\ProductVariant\Image');

            return $repo->countByProduct($product);
        }

        return $result;
    }
}
