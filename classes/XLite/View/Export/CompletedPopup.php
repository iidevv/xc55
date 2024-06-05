<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Export;

/**
 * Completed section
 */
class CompletedPopup extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'export/completed_popup.twig';
    }

    /**
     * Get message which is shown after export
     *
     * @return string
     */
    protected function getCompleteMessage()
    {
        return '';
    }
}
