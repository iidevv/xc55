<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Methods extends \XLite\View\Tabs\ShippingSettings
{
    /**
     * Get list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (\XLite\Core\Request::getInstance()->processor === 'usps') {
            $list[] = 'modules/CDev/USPS/style.css';
        }

        return $list;
    }
}
