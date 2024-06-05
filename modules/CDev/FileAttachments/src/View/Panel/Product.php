<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\View\Panel;

/**
 * Product attachments panel
 */
class Product extends \XLite\View\Base\StickyPanel
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/FileAttachments/panel';
    }
}
