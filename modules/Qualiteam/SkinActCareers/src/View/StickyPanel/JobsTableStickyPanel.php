<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\StickyPanel;


class JobsTableStickyPanel extends \XLite\View\StickyPanel\ItemsListForm
{

    protected function defineButtons()
    {
        $list = parent::defineButtons();

        $list['delete'] = $this->getWidget(
            ['style' => 'always-enabled action link list-action',],
            '\Qualiteam\SkinActCareers\View\Button\DeleteSelected'
        );

        return $list;
    }
}