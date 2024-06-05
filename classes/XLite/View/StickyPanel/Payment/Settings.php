<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Payment;

/**
 * Payment method settings sticky panel
 */
class Settings extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'sticky_panel/payment/settings.css';

        return $list;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['addons-list'] = $this->getWidget(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to methods'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action addons-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('payment_settings'),
            ],
            '\XLite\View\Button\SimpleLink'
        );

        return $list;
    }
}
