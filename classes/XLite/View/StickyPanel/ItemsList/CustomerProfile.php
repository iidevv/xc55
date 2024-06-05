<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\ItemsList;

class CustomerProfile extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * @return array
     */
    protected function defineButtons()
    {
        $list           = parent::defineButtons();
        $list['export'] = $this->getWidget(
            [],
            'XLite\View\Button\ItemsExport\CustomerProfile'
        );

        return $list;
    }
}
