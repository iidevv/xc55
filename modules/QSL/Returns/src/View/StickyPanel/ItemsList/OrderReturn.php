<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\StickyPanel\ItemsList;

use XLite\View\AView;
use XLite\View\Button\Submit;
use XLite\View\StickyPanel\ItemsListForm;

/**
 * Returns items list's sticky panel
 */
class OrderReturn extends ItemsListForm
{
    /**
     * Get "save" widget
     *
     * @return Submit | AView
     */
    protected function getSaveWidget()
    {
        return $this->getWidget(
            [
                'style'    => 'regular-main-button action submit hide-if-empty-list',
                'label'    => static::t('Save changes'),
                'disabled' => true,
            ],
            'QSL\Returns\View\Button\Admin\OrderReturns\Delete'
        );
        return $widget;
    }

    protected function getSettingLinkClassName(): string
    {
        return parent::getSettingLinkClassName() ?: '\QSL\Returns\Main';
    }
}
