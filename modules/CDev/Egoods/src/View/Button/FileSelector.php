<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class FileSelector extends \XLite\View\Button\FileSelector
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Egoods/file_selector.css';

        return $list;
    }
}
