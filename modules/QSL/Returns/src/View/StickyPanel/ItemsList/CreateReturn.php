<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\StickyPanel\ItemsList;

/**
 * Create return items list's sticky panel
 */
class CreateReturn extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Get "save" widget
     *
     * @return \XLite\View\Button\Submit
     */
    protected function getSaveWidget()
    {
        $widget = $this->getWidget(
            [
                'style' => 'action submit always-enabled',
                'label' => $this->getSaveWidgetLabel(),
                'disabled' => false,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => $this->getSaveWidgetStyle(),
            ],
            'QSL\Returns\View\Button\Admin\ModifyReturn\Submit'
        );

        return $widget;
    }

    /**
     * Defines the label for the save button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Submit return');
    }
}
