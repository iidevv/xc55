<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductComparison\View\Button;

/**
 * Compare button
 *
 */
class Compare extends \XLite\View\Button\Link
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ProductComparison/button/compare/style.css';

        return $list;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass() . ' action compare';
        if (1 >= \XC\ProductComparison\Core\Data::getInstance()->getProductsCount()) {
            $class .= ' disabled';
        }

        return trim($class);
    }

    /**
     * Return button text
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return 'Compare';
    }

    /**
     * JavaScript: this code will be used by default
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return 'if (jQuery(this).hasClass(\'disabled\')) return false; else self.location = \''
            . $this->buildURL('compare') . '\';';
    }
}
