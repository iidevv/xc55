<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product list item widget
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/FreeShipping/label/style.css';

        return $list;
    }

    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        return parent::getLabels() + \XC\FreeShipping\Core\Labels::getLabel($this->getProduct());
    }
}
