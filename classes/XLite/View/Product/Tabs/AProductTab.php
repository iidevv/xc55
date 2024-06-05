<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Tabs;

/**
 * Abstract Product tab
 */
abstract class AProductTab extends \XLite\View\AView
{
    public const PARAM_TAB = 'tab';

    /**
     * Check if tab available to display
     *
     * @param \XLite\Model\Product $product
     *
     * @return boolean
     */
    public static function isAvailable(\XLite\Model\Product $product)
    {
        return true;
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_TAB => new \XLite\Model\WidgetParam\TypeObject(
                'Tab',
                null,
                false,
                '\XLite\Model\Product\GlobalTab'
            ),
        ];
    }

    /**
     * Returns tab
     *
     * @return \XLite\Model\Product\GlobalTab
     */
    protected function getTab()
    {
        return $this->getParam(self::PARAM_TAB);
    }
}
