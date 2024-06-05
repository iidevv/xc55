<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XLite\View\StickyPanel\Payment;

use XLite\View\Base\FormStickyPanel;
use XLite\View\Button\AButton;
use XLite\View\Button\Link;
use XLite\View\Button\SimpleLink;

/**
 * Panel for event progress page.
 */
class AddOfflineMethod extends FormStickyPanel
{
    /**
     * The panel buttons will be cached here.
     */
    protected ?array $buttons = null;

    /**
     * Get the panel buttons.
     */
    protected function getButtons(): array
    {
        if ($this->buttons === null) {
            $this->buttons = $this->defineButtons();
        }

        return $this->buttons;
    }

    /**
     * Define the sticky panel buttons.
     */
    protected function defineButtons(): array
    {
        return [
            'add'             => $this->getWidget(
                [
                    AButton::PARAM_STYLE    => 'action submit',
                    AButton::PARAM_LABEL    => static::t('Add'),
                    AButton::PARAM_DISABLED => true,
                    AButton::PARAM_BTN_TYPE => 'regular-main-button',
                ],
                'XLite\View\Button\Submit'
            ),
            'back_to_methods' => new SimpleLink(
                [
                    AButton::PARAM_LABEL => static::t('Back to methods'),
                    AButton::PARAM_STYLE => 'action methods-back-button',
                    Link::PARAM_LOCATION => $this->buildURL('payment_settings'),
                ]
            ),
        ];
    }
}
