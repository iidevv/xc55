<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product;

use XCart\Extender\Mapping\ListChild;

/**
 * Variants
 *
 * @ListChild (list="admin.product.variants", zone="admin", weight="30")
 */
class Variants extends \XC\ProductVariants\View\Product\AProduct
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/variants';
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
        $count = count($this->getProduct()->getVariants());

        return $count == 0
            ? static::t('No variants added, and the product is not available for sale yet. Create at least one variant.')
            : static::t(
                '{{count}} variants',
                [
                    'count' => $count
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
        return parent::getBlockStyle() . ' variants';
    }

    /**
     * Return true if variants limit warning must be displayed
     *
     * @return boolean
     */
    protected function isLimitWarningVisible()
    {
        return $this->getVariantsNumberSoftLimit() < count($this->getProduct()->getVariants());
    }
}
