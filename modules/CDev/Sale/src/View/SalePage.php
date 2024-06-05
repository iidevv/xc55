<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Sale products list widget
 *
 * @ListChild (list="center")
 */
class SalePage extends \CDev\Sale\View\ASale
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = self::WIDGET_TARGET_SALE_PRODUCTS;

        return $result;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'CDev\Sale\View\Pager\Customer\ControllerPager';
    }

    /**
     * Returns empty widget head title (controller page header will be used instead)
     *
     * @return string
     */
    protected function getHead()
    {
        return '';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
        && static::getWidgetTarget() === \XLite\Core\Request::getInstance()->target;
    }
}
