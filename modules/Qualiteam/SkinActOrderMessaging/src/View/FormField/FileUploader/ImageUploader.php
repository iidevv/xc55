<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\FormField\FileUploader;


class ImageUploader extends \XLite\View\FormField\FileUploader\Image
{
    protected function getDir()
    {
        return '';
    }

    protected function getFieldTemplate()
    {
        if ($this->mode === 1) {
            return 'modules/Qualiteam/SkinActOrderMessaging/uploader/file_uploader/multiple1.twig';
        }
        return 'modules/Qualiteam/SkinActOrderMessaging/uploader/file_uploader/multiple.twig';
    }
}