<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\Menu\Admin;

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
        if (!isset($this->relatedTargets['surveys'])) {
            $this->relatedTargets['surveys'] = [];
        }

        $this->relatedTargets['surveys'][] = 'questions';
        $this->relatedTargets['surveys'][] = 'survey';

        parent::__construct();
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['communications'])) {
            $list['communications'][static::ITEM_CHILDREN]['surveys'] = [
                static::ITEM_TITLE  => new TitleFromController('surveys'),
                static::ITEM_TARGET => 'surveys',
                static::ITEM_WEIGHT   => 160,
            ];
        }

        return $list;
    }
}
