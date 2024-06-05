<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\StickyPanel;

/**
 * Facebook Marketing settings sticky panel
 */
class Settings extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['generate'] = $this->getGenerateWidget();

        return $list;
    }

    /**
     * Get "Generate" button
     *
     * @return \XLite\View\AView
     */
    protected function getGenerateWidget()
    {
        return $this->getWidget(
            [
                'style'    => 'btn regular-button action always-enabled generate-button',
                'label'    => static::t('Generate Product Feed'),
                \XLite\View\Button\Link::PARAM_LOCATION => static::buildURL(
                    'facebook_marketing',
                    'Generate'
                ),
                'disabled' => false,
            ],
            'XLite\View\Button\Link'
        );
    }
}
