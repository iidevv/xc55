<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\News\Module\CDev\SimpleCMS\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Converter;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\SimpleCMS")
 */
class NewsMessage extends \XC\News\Controller\Admin\NewsMessage
{
    protected function getUpdateActionReturnPage(): string
    {
        return Auth::getInstance()->isPermissionAllowed('manage custom pages')
            ? Converter::buildURL(
                'pages',
                '',
                [
                    'page' => 'news_messages'
                ]
            )
            : Converter::buildURL('news_messages');
    }
}
