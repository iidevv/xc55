<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->addRelatedTarget('news_message', 'pages', [], ['page' => 'primary']);
    }

    protected function defineItems()
    {
        $list = parent::defineItems();

        if (
            !Auth::getInstance()->isPermissionAllowed('manage custom pages')
            && Auth::getInstance()->isPermissionAllowed('manage news')
            && !isset($list['store_design'][static::ITEM_CHILDREN]['news_messages'])
        ) {
            $list['store_design'][static::ITEM_CHILDREN]['news_messages'] = [
                static::ITEM_TITLE      => static::t('News messages'),
                static::ITEM_TARGET     => 'news_messages',
                static::ITEM_PERMISSION => 'manage news',
            ];
        }

        return $list;
    }
}
