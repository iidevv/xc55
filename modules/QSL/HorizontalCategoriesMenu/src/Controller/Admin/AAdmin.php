<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract admin-zone controller
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Call controller action
     *
     * @return void
     */
    protected function callAction()
    {
        parent::callAction();

        if ($this->isLogged()) {
            $category = new \XLite\Model\Category();
            $category->publicCleanDTOsCache();
        }
    }
}
