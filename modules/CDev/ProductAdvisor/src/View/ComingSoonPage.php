<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Coming soon products list widget
 *
 * @ListChild (list="center")
 */
class ComingSoonPage extends \CDev\ProductAdvisor\View\AComingSoon
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = self::WIDGET_TARGET_COMING_SOON;

        return $result;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'CDev\ProductAdvisor\View\Pager\Customer\ControllerPager';
    }

    /**
     * Return no head for widget (controller header will be used instead)
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
        return static::getWidgetTarget() === \XLite\Core\Request::getInstance()->target
        && parent::isVisible();
    }
}
