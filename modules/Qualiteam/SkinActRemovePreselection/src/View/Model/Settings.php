<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActRemovePreselection\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Settings extends \XLite\View\Model\Settings
{
    protected function getModelObjectValue($name)
    {
        if ($name === 'force_choose_product_options') {
            return 'product_page';
        }

        return parent::getModelObjectValue($name);
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActRemovePreselection/disable_option.js';
        return $list;
    }
}