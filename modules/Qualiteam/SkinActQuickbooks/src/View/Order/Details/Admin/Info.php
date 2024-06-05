<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\Order\Details\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Order info
 * 
 * @Extender\Mixin
 */
class Info extends \XLite\View\Order\Details\Admin\Info
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActQuickbooks/order/invoice/style.css';

        return $list;
    }
}