<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Module\CDev\SimpleCMS\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 * @Extender\Before ("XC\News")
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Pages extends \CDev\SimpleCMS\Controller\Admin\Pages
{
    /**
     * @return array
     */
    public function getPages()
    {
        if (Auth::getInstance()->isPermissionAllowed('manage catalog')) {
            return array_merge(
                parent::getPages(),
                [
                    'global_tabs' => static::t('Product page tabs')
                ]
            );
        }

        return parent::getPages();
    }

    /**
     * @return array
     */
    protected function getPageTemplates()
    {
        if (Auth::getInstance()->isPermissionAllowed('manage catalog')) {
            return array_merge(
                parent::getPageTemplates(),
                [
                    'global_tabs' => 'modules/XC/CustomProductTabs/global_tabs/list.twig'
                ]
            );
        }

        return parent::getPageTemplates();
    }
}
