<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender as Extender;
use XCart\Extender\Mapping\ListChild;

/**
 * Main
 * @ListChild (list="center", zone="customer")
 *
 * @Extender\Mixin
 *
 */
class Main extends \XLite\View\Product\Details\Customer\Page\Main
{
    /**
     * Check - loupe icon is visible or not
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        if (!static::hasProductSpin($this->getProduct())) {
            return parent::isLoupeVisible();
        }

        return false;
    }

    /**
     * Define CSS class for Magic360 module
     *
     * @return string
     */
    protected function getMagicToolboxClass()
    {
        $class = 'magic360-image-block';
        if (static::hasProductSpin($this->getProduct())) {
            return $class;
        }
        if (method_exists(get_parent_class(), 'getMagicToolboxClass')) {
            return parent::getMagicToolboxClass();
        }

        return '';
    }
}
