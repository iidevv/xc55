<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\News\Module\CDev\SimpleCMS\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("!CDev\SimpleCMS")
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (!isset($list['store_design'][static::ITEM_CHILDREN]['news_messages'])) {
            $list['store_design'][static::ITEM_CHILDREN]['news_messages'] = [
                static::ITEM_TITLE      => static::t('News messages'),
                static::ITEM_TARGET     => 'news_messages',
                static::ITEM_PERMISSION => 'manage news',
            ];
        }

        return $list;
    }
}
