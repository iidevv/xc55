<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\View\StickyPanel;

/**
 * Search subscribers sticky panel
 */
class Subscribers extends \XLite\View\StickyPanel\ItemsListForm
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
            'XC\NewsletterSubscriptions\View\Button\ItemsExport\Subscribers'
        );
        return $list;
    }
}
