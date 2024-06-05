<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\View\Order\Details\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Order info
 * @Extender\Mixin
 */
abstract class Info extends \XLite\View\Order\Details\Admin\Info
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/CustomerAttachments/script.js';

        return $list;
    }
}
