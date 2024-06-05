<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

use XCart\Extender\Mapping\ListChild;

/**
 * More addons link
 *
 * @ListChild (list="modules.pager.buttons", weight="100", zone="admin")
 */
class AllAddons extends \XLite\View\Button\Link
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Request::getInstance()->target === 'addons_list_installed'
            && isset(\XLite\Core\Request::getInstance()->recent);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_LOCATION]->setValue($this->buildURL('addons_list_installed'));
        $this->widgetParams[self::PARAM_LABEL]->setValue('Show all');
        $this->widgetParams[self::PARAM_STYLE]->setValue('more-addons-button');
    }
}
