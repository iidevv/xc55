<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel form item-based form with product preview
 */
class PanelWithOnlyPreview extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Get buttons
     *
     * @return array
     */
    public function getButtons()
    {
        $buttons['preview-product'] = $this->getProductPreviewWidget();

        return $buttons;
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && empty(\XLite\Core\Request::getInstance()->spage)
            && empty(\XLite\Core\Request::getInstance()->subpage);
    }
}
