<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\ItemsList;

/**
 * Remove data items list's sticky panel
 */
class RemoveData extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Get "save" widget
     *
     * @return \XLite\View\Button\Submit
     */
    protected function getSaveWidget()
    {
        return $this->getWidget(
            [
                'style'    => 'action submit',
                'label'    => $this->getSaveWidgetLabel(),
                'disabled' => true,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => $this->getSaveWidgetStyle(),
            ],
            'XLite\View\Button\Submit\RemoveData'
        );
    }

    protected function getSaveWidgetLabel()
    {
        return static::t('Remove data');
    }
}
