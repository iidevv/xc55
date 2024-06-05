<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Form;

use XCart\Extender\Mapping\Extender;

/**
 * Images settings form
 * @Extender\Mixin
 */
class ImagesSettings extends \XLite\View\Form\ImagesSettings
{
    /**
     * Add the 'enctype="multipart/form-data"' form attribute
     *
     * @return boolean
     */
    protected function isMultipart()
    {
        return true;
    }
}
