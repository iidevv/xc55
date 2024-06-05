<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\FormField\Inline\FileUploader;

/**
 * Image
 */
class Image extends \XLite\View\FormField\Inline\FileUploader\Image
{
    protected function isEditable()
    {
        return true;
    }

    protected function getSavedValue()
    {
        return $this->getEntity();
    }

    protected function getEntityValue()
    {
        return $this->getEntity();
    }
}
