<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\XMLSitemap\View\StickyPanel;

class ItemForm extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list['generate'] = [
            'class'    => 'XLite\View\Button\Link',
            'params'   => [
                'disabled' => false,
                'label'    => 'Generate XML-Sitemap',
                'style'    => 'action always-enabled',
                'location' => $this->buildURL('sitemap', 'Generate'),

            ],
            'position' => 100,
        ];

        return $list;
    }
}
