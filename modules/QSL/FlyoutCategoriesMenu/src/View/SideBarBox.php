<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\FlyoutCategoriesMenu\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XLite\Core\Config;

/**
 * @Extender\Mixin
 */
abstract class SideBarBox extends \XLite\View\SideBarBox
{
    /**
     * @return integer
     */
    protected static function getAllowedDepthWithoutAjax()
    {
        return (int) Config::getInstance()->QSL->FlyoutCategoriesMenu->allowed_depth_without_ajax;
    }

    /**
     * @return int
     */
    protected function getCategoryId()
    {
        return (int) Request::getInstance()->category_id ?? $this->getRootCategoryId();
    }

    /**
     * @param int $depth
     *
     * @return bool
     */
    protected function isAllowedDepthWithoutAjax(int $depth)
    {
        return $depth < static::getAllowedDepthWithoutAjax() - 1;
    }
}
