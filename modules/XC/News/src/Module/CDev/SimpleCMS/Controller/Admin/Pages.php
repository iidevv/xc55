<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Module\CDev\SimpleCMS\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Pages extends \CDev\SimpleCMS\Controller\Admin\Pages
{
    /**
     * @return array
     */
    public function getPages()
    {
        if (Auth::getInstance()->isPermissionAllowed('manage news')) {
            return array_merge(
                parent::getPages(),
                [
                    'news_messages' => static::t('News messages')
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
        if (Auth::getInstance()->isPermissionAllowed('manage news')) {
            return array_merge(
                parent::getPageTemplates(),
                [
                    'news_messages' => 'modules/XC/News/news_messages/body.twig'
                ]
            );
        }

        return parent::getPageTemplates();
    }
}
