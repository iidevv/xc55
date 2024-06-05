<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    /**
     * Get class name of the settings form.
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return ($this->getModuleId() === 'QSL-ShopByBrand')
            ? 'QSL\ShopByBrand\View\Model\ModuleSettings'
            : parent::getModelFormClass();
    }
}
