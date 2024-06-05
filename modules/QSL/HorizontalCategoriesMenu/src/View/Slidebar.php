<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View;

use XCart\Extender\Mapping\Extender;

/**
 * Welcome page
 *
 * @Extender\Mixin
 */
class Slidebar extends \XLite\View\Slidebar
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/HorizontalCategoriesMenu/slidebar.twig';
    }

    /**
     * Check if display Home link
     *
     * @return boolean
     */
    protected function isShowHomeLink()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_show_home;
    }
}
