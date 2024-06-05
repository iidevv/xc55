<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\View;

use XCart\Extender\Mapping\ListChild;

/**
 * New arrivals products list widget
 *
 * @ListChild (list="center", zone="customer")
 */
class BestsellersPage extends \CDev\Bestsellers\View\ABestsellers
{
    /**
     * Widget target
     */
    public const WIDGET_TARGET = 'bestsellers';

    /**
     * Return target to retrieve this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return self::WIDGET_TARGET;
    }

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = self::WIDGET_TARGET;

        return $result;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'CDev\Bestsellers\View\Pager\Customer\ControllerPager';
    }

    /**
     * Returns empty widget head title (controller page header will be used instead)
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }
}
