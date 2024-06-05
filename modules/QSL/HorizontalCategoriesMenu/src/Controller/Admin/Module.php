<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Module settings
 * @Extender\Mixin
 */
abstract class Module extends \XLite\Controller\Admin\Module
{
    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        if (
            $this->getModuleID() === 'QSL-HorizontalCategoriesMenu'
        ) {
            $category = new \XLite\Model\Category();
            $category->publicCleanDTOsCache();
        }

        parent::handleRequest();
    }
}
