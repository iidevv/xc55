<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View;

use XCart\Extender\Mapping\ListChild;

/**
 * New arrivals products list widget
 *
 * @ListChild (list="center")
 */
class NewArrivalsPage extends \CDev\ProductAdvisor\View\ANewArrivals
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = self::WIDGET_TARGET_NEW_ARRIVALS;

        return $result;
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $this->widgetParams[\XLite\View\Pager\APager::PARAM_MAX_ITEMS_COUNT]->setValue($this->getMaxCountInFullList());
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
        return static::getWidgetTarget() === \XLite\Core\Request::getInstance()->target
            && parent::isVisible();
    }
}
