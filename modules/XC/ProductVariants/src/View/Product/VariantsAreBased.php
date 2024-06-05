<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product;

use XCart\Extender\Mapping\ListChild;

/**
 * Variants are based
 *
 * @ListChild (list="admin.product.variants", zone="admin", weight="20")
 */
class VariantsAreBased extends \XC\ProductVariants\View\Product\AProduct
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/variants_are_based';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getVariantsAttributes();
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getTitle()
    {
        $variants = [];

        foreach ($this->getVariantsAttributes() as $v) {
            $variants[] = $v->getName();
        }

        return static::t(
            '{{count}} variants are based on {{variants}}',
            [
                'count' => count($this->getProduct()->getVariants()),
                'variants' => '<span>' . implode('</span> <span>', $variants) . '</span>',
            ]
        );
    }

    /**
     * Return block style
     *
     * @return string
     */
    protected function getBlockStyle()
    {
        return parent::getBlockStyle() . ' variants-are-based';
    }
}
