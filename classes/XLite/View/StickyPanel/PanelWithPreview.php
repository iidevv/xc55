<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel form item-based form with product preview
 */
class PanelWithPreview extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Set buttons
     *
     * @param array $buttons Buttons
     *
     * @return void
     */
    public function setButtons($buttons)
    {
        if ($this->isProductPreviewWidgetVisible()) {
            $buttons['preview-product'] = $this->getProductPreviewWidget();
        }

        $this->buttonsList = $buttons;
    }
}
