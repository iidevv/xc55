<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCreateOrder\Controller\Admin;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class ModelProfileSelector extends \XLite\Controller\Admin\ModelProfileSelector
{
    protected function defineDataItem($item)
    {
        $data = parent::defineDataItem($item);
        $data['pid'] = $item->getProfileId();
        return $data;
    }
}