<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\Controller\Admin;

use Qualiteam\SkinActContactUsPage\Helper\GoogleConfiguration;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        if ($this->getModuleId() === 'Qualiteam-SkinActContactUsPage') {
            (new GoogleConfiguration())->updateGoogleData();
        }
    }
}
