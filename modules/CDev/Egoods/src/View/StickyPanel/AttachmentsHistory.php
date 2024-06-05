<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\StickyPanel;

/**
 * Panel for AttachmentsHistory subpage.
 */
class AttachmentsHistory extends \XLite\View\StickyPanel\PanelWithOnlyPreview
{
    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }
}
