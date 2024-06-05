<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWishlistUserExport\View\StickyPanel;


class WishlistStickyPanel extends \XLite\View\StickyPanel\ItemsListForm
{

    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget(
            [],
            'Qualiteam\SkinActWishlistUserExport\View\Button\ItemsExport\WishlistExport'
        );

        return $list;
    }
}