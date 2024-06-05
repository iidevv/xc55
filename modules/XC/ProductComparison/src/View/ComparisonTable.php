<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductComparison\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Main
 *
 * @ListChild (list="center", zone="customer")
 */
class ComparisonTable extends \XC\ProductComparison\View\ComparisonTable\AComparisonTable
{
    /**
     * Style cache
     *
     * @var string
     */
    protected static $style;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'compare';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/script.js';

        return $list;
    }

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
     * Get style
     *
     * @return string
     */
    protected function getStyle()
    {
        if (!isset(static::$style)) {
            $count = count($this->getProducts()) + 1;
            static::$style = 6 > $count
                ? 'width:' . round(100 / $count) . '%'
                : '';
        }
        return static::$style;
    }

    protected function getProductButtonWidget(\XLite\Model\Product $product)
    {
        return $this->getWidget([
            AddToCart::PARAM_PRODUCT => $product
        ], '\XC\ProductComparison\View\AddToCart');
    }
}
