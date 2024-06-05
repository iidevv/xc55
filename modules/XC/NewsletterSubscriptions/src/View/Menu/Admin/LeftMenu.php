<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Controller\TitleFromController;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->relatedTargets['notifications'][] = 'newsletter_subscribers';

        parent::__construct($params);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['communications'])) {
            $list['communications'][static::ITEM_CHILDREN]['newsletter_subscribers'] = [
                static::ITEM_TITLE  => new TitleFromController('newsletter_subscribers'),
                static::ITEM_TARGET => 'newsletter_subscribers',
                static::ITEM_WEIGHT   => 210,
            ];
        }

        return $list;
    }
}
