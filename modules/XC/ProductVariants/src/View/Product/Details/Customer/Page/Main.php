<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * Main
 * @Extender\Mixin
 */
class Main extends \XLite\View\Product\Details\Customer\Page\Main
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ProductVariants/product/controller.js';

        return $list;
    }

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        $list = parent::getContainerAttributes();
        $product = $this->getProduct();
        $repo = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\Image\ProductVariant\Image');

        if (
            $product->mustHaveVariants()
            && 0 < $repo->countByProduct($product)
        ) {
            $list['data-variants-has-images'] = true;
        }

        return $list;
    }
}
