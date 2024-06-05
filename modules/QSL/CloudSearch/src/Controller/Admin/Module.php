<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace QSL\CloudSearch\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Module settings
 *
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->getModuleId() === 'QSL-CloudSearch') {
            return static::t('Search & Filters');
        }

        return parent::getTitle();
    }
}
