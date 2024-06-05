<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Product\Admin;

/**
 * Cloned products sticky panel
 */
class Cloned extends \XLite\View\StickyPanel\Product\Admin\AAdmin
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget(
            [],
            'XLite\View\Button\ItemsExport\ClonedProducts'
        );
        return $list;
    }
}
