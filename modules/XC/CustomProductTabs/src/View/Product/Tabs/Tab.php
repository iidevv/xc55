<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\View\Product\Tabs;

/**
 * Product tab page view
 *
 */
class Tab extends \XLite\View\Product\Tabs\AProductTab
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CustomProductTabs/tab.twig';
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_TAB     => new \XLite\Model\WidgetParam\TypeObject(
                'Tab',
                null,
                false,
                '\XC\CustomProductTabs\Model\Product\Tab'
            ),
        ];
    }

    /**
     * Returns tab
     *
     * @return \XC\CustomProductTabs\Model\Product\Tab
     */
    protected function getTab()
    {
        return $this->getParam(self::PARAM_TAB);
    }
}
