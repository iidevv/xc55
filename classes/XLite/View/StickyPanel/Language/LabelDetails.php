<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Language;

/**
 * Panel for Language label details form.
 */
class LabelDetails extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Get buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        $buttons = [];
        $buttons['save'] = $this->getWidget(
            [
                'style'    => 'action regular-main-button',
                'label'    => static::t('Save changes'),
                'disabled' => false,
            ],
            'XLite\View\Button\Submit'
        );

        return $buttons;
    }
}
